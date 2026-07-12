<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentProof extends Model
{
    protected $fillable = [
        'order_id',
        'method',
        'sender_number',
        'transaction_id',
        'screenshot',
        'customer_note',
        'status',
        'verified_by',
        'verified_at',
        'admin_note',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getScreenshotUrlAttribute(): ?string
    {
        return $this->screenshot ? \Storage::disk('public')->url($this->screenshot) : null;
    }

    /** Admin approves this payment proof -> cascades to the order */
    public function markVerified(User $admin): void
    {
        $this->update([
            'status' => 'verified',
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        $this->order->update([
            'payment_status' => 'verified',
            'status' => 'confirmed',
        ]);
    }

    /** Admin rejects this payment proof -> cascades to the order */
    /** Admin rejects this payment proof -> cascades to the order */
    public function markRejected(User $admin, ?string $reason = null): void
    {
        $this->update([
            'status' => 'rejected',
            'verified_by' => $admin->id,
            'verified_at' => now(),
            'admin_note' => $reason,
        ]);

        // Don't downgrade an order whose payment has already been verified —
        // a later unrelated rejected transaction ID shouldn't undo confirmed payment.
        if ($this->order->payment_status !== 'verified') {
            $this->order->update(['payment_status' => 'rejected']);
        }
    }


    protected static function booted(): void
{
    static::created(function (PaymentProof $proof) {
        if ($proof->order && $proof->order->payment_status === 'pending') {
            $proof->order->update(['payment_status' => 'submitted']);
        }
    });
}
}