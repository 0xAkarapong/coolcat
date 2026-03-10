<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatBreed extends Model
{
    /** @use HasFactory<\Database\Factories\CatBreedFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'name_th',
        'origin',
        'image',
    ];

    public function catListings(): HasMany
    {
        return $this->hasMany(CatListing::class, 'breed_id');
    }
}
