<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /** Redirect the user to Google's OAuth consent screen */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /** Handle the callback from Google after the user approves */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::error('Google login failed: ' . $e->getMessage());

            return redirect()->route('login')
                ->with('error', 'Google login failed. Please try again.');
        }

        // 1. Try to find an existing user by google_id
        $user = User::where('google_id', $googleUser->getId())->first();

        // 2. If not found, try matching by email (user may have registered manually before)
        if (!$user) {
            $user = User::where('email', $googleUser->getEmail())->first();
        }

        if ($user) {

            $user->update([
                'google_id' => $googleUser->getId(),
                'name' => $googleUser->getName() ?? $user->name,
                'avatar' => $googleUser->getAvatar(),
            ]);

        } else {
            // 3. Brand new user
            $user = User::create([
                'name' => $googleUser->getName() ?? $googleUser->getNickname(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => Str::random(24), // random, unused — they always login via Google
                'email_verified_at' => now(),
                'role' => 'customer',
                'is_active' => true,
            ]);
        }

        if (!$user->is_active) {
            return redirect()->route('login')->with('error', 'Your account has been disabled.');
        }

        Auth::login($user, remember: true);

        $this->mergeGuestCartInto($user);

        return redirect()->intended(route('home'));
    }

    /**
     * Merges a guest's cart_token-based cart lines into their now-authenticated account.
     * If the same product+variant already exists in the account's cart, quantities are
     * combined (capped by available stock); otherwise the guest line is simply reassigned.
     */
    private function mergeGuestCartInto(User $user): void
    {
        $token = request()->cookie('cart_token');

        if (!$token) {
            return;
        }

        $guestLines = Cart::where('user_id', null)->where('cart_token', $token)->get();

        foreach ($guestLines as $guestLine) {
            $existing = Cart::where('user_id', $user->id)
                ->where('product_id', $guestLine->product_id)
                ->where('product_variant_id', $guestLine->product_variant_id)
                ->first();

            $availableStock = $guestLine->variant ? $guestLine->variant->stock : $guestLine->product->stock;

            if ($existing) {
                $existing->update([
                    'quantity' => min($existing->quantity + $guestLine->quantity, $availableStock, 20),
                ]);
                $guestLine->delete();
            } else {
                $guestLine->update([
                    'user_id' => $user->id,
                    'cart_token' => null,
                    'quantity' => min($guestLine->quantity, $availableStock, 20),
                ]);
            }
        }
    }
}