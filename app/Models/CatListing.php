<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatListing extends Model
{
    /** @use HasFactory<\Database\Factories\CatListingFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'breed_id',
        'name',
        'gender',
        'birthdate',
        'color',
        'description',
        'image',
        'type',
        'price',
        'status',
        'is_neutered',
        'is_vaccinated',
        'province',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'birthdate' => 'date',
            'is_neutered' => 'boolean',
            'is_vaccinated' => 'boolean',
            'views' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(CatBreed::class, 'breed_id');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(CatInquiry::class, 'listing_id');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeForSale(Builder $query): Builder
    {
        return $query->where('type', 'sale');
    }

    public function scopeForAdoption(Builder $query): Builder
    {
        return $query->where('type', 'adoption');
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birthdate?->diffInMonths(now());
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        })
            ->when($filters['breed_id'] ?? null, fn ($q, $breed) => $q->where('breed_id', $breed))
            ->when($filters['type'] ?? null, fn ($q, $type) => $q->where('type', $type))
            ->when($filters['province'] ?? null, fn ($q, $province) => $q->where('province', $province))
            ->when($filters['min_price'] ?? null, fn ($q, $min) => $q->where('price', '>=', $min))
            ->when($filters['max_price'] ?? null, fn ($q, $max) => $q->where('price', '<=', $max));
    }
}
