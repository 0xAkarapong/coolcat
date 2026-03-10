<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatInquiry extends Model
{
    /** @use HasFactory<\Database\Factories\CatInquiryFactory> */
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'buyer_id',
        'message',
        'meet_date',
        'meet_time',
        'meet_location',
        'status',
        'seller_note',
    ];

    protected function casts(): array
    {
        return [
            'meet_date' => 'date',
        ];
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(CatListing::class, 'listing_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }
}
