<?php

namespace Database\Factories;

use App\Models\CatListing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CatInquiry>
 */
class CatInquiryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'listing_id' => CatListing::factory(),
            'buyer_id' => User::factory(),
            'message' => fake()->paragraph(),
            'meet_date' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'meet_time' => fake()->time('H:i'),
            'meet_location' => fake()->address(),
            'status' => 'pending',
            'seller_note' => null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
