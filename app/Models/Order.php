<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAYMENT_UPLOADED = 'payment_uploaded';
    public const STATUS_PAID = 'paid';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'product_id',
        'coupon_id',
        'invoice_number',
        'buyer_name',
        'buyer_email',
        'buyer_whatsapp',
        'price',
        'status',
        'payment_method',
        'coupon_code',
        'discount_amount',
        'original_price',
        'payment_proof_path',
        'payment_uploaded_at',
        'payment_note',
        'paid_at',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejection_reason',
        'download_token',
        'download_expires_at',
        'download_count',
        'admin_notes',
    ];

    protected $casts = [
        'price' => 'integer',
        'discount_amount' => 'decimal:2',
        'original_price' => 'decimal:2',
        'payment_uploaded_at' => 'datetime',
        'paid_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'download_expires_at' => 'datetime',
        'download_count' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function downloadLogs(): HasMany
    {
        return $this->hasMany(DownloadLog::class);
    }

    public function auditTrails(): HasMany
    {
        return $this->hasMany(OrderAuditTrail::class);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function hasUploadedPayment(): bool
    {
        return $this->status === self::STATUS_PAYMENT_UPLOADED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
