<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::query();

        if ($search = $request->input('search')) {
            $query->where('code', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->whereNotNull('expires_at')->where('expires_at', '<', now()->toDateString());
            }
        }

        $coupons = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'    => Coupon::count(),
            'active'   => Coupon::where('is_active', true)
                                 ->where(function ($q) {
                                     $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()->toDateString());
                                 })->count(),
            'expired'  => Coupon::whereNotNull('expires_at')->where('expires_at', '<', now()->toDateString())->count(),
            'redeemed' => Coupon::sum('used_count'),
        ];

        if ($request->ajax()) {
            return view('admin.coupons.partials.table', compact('coupons'))->render();
        }

        return view('admin.coupons.index', compact('coupons', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $this->validateCoupon($request);

        $data['code'] = strtoupper($data['code']);

        $coupon = Coupon::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Coupon created successfully.',
            'coupon'  => $coupon,
        ]);
    }

    public function edit(Coupon $coupon)
    {
        return response()->json($coupon);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $data = $this->validateCoupon($request, $coupon->id);

        $data['code'] = strtoupper($data['code']);

        $coupon->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Coupon updated successfully.',
            'coupon'  => $coupon,
        ]);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Coupon deleted successfully.',
        ]);
    }

    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update(['is_active' => ! $coupon->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $coupon->is_active,
            'message'   => 'Coupon status updated.',
        ]);
    }

    private function validateCoupon(Request $request, ?int $ignoreId = null): array
    {
        $rules = [
            'code'              => [
                'required', 'string', 'max:50', 'alpha_dash',
                'unique:coupons,code' . ($ignoreId ? ",{$ignoreId}" : ''),
            ],
            'type'              => ['required', 'in:fixed,percent'],
            'value'             => ['required', 'numeric', 'min:0.01'],
            'min_order_amount'  => ['nullable', 'numeric', 'min:0'],
            'usage_limit'       => ['nullable', 'integer', 'min:1'],
            'expires_at'        => ['nullable', 'date', 'after_or_equal:today'],
            'is_active'         => ['nullable', 'boolean'],
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            if ($request->input('type') === 'percent' && $request->filled('value') && $request->value > 100) {
                $validator->errors()->add('value', 'Percentage discount cannot exceed 100.');
            }
        });

        if ($validator->fails()) {
            abort(response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422));
        }

        $data = $validator->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['min_order_amount'] = $data['min_order_amount'] ?? 0;

        return $data;
    }
}