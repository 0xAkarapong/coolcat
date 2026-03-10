<?php

namespace Database\Factories;

use App\Models\CatBreed;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CatListing>
 */
class CatListingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['adoption', 'sale']);

        return [
            'user_id' => User::factory(),
            'breed_id' => CatBreed::factory(),
            'name' => fake()->firstName(),
            'gender' => fake()->randomElement(['male', 'female', 'unknown']),
            'birthdate' => fake()->dateTimeBetween('-5 years', '-1 month')->format('Y-m-d'),
            'color' => fake()->randomElement(['white', 'black', 'orange', 'grey', 'tabby', 'calico', 'cream']),
            'description' => fake()->paragraph(),
            'image' => null,
            'type' => $type,
            'price' => $type === 'sale' ? fake()->randomFloat(2, 500, 50000) : null,
            'status' => 'active',
            'is_neutered' => fake()->boolean(30) ? 'true' : 'false',
            'is_vaccinated' => fake()->boolean(60) ? 'true' : 'false',
            'views' => fake()->numberBetween(0, 200),
            'province' => fake()->randomElement([
                'Amnat Charoen', 'Ang Thong', 'Bangkok', 'Bueng Kan', 'Buri Ram', 'Chachoengsao', 'Chai Nat', 'Chaiyaphum', 'Chanthaburi', 'Chiang Mai', 'Chiang Rai', 'Chon Buri', 'Chumphon', 'Kalasin', 'Kamphaeng Phet', 'Kanchanaburi', 'Khon Kaen', 'Krabi', 'Lampang', 'Lamphun', 'Loei', 'Lop Buri', 'Mae Hong Son', 'Maha Sarakham', 'Mukdahan', 'Nakhon Nayok', 'Nakhon Pathom', 'Nakhon Phanom', 'Nakhon Ratchasima', 'Nakhon Sawan', 'Nakhon Si Thammarat', 'Nan', 'Narathiwat', 'Nong Bua Lam Phu', 'Nong Khai', 'Nonthaburi', 'Pathum Thani', 'Pattani', 'Phangnga', 'Phatthalung', 'Phayao', 'Phetchabun', 'Phetchaburi', 'Phichit', 'Phitsanulok', 'Phra Nakhon Si Ayutthaya', 'Phrae', 'Phuket', 'Prachin Buri', 'Prachuap Khiri Khan', 'Ranong', 'Ratchaburi', 'Rayong', 'Roi Et', 'Sa Kaeo', 'Sakon Nakhon', 'Samut Prakan', 'Samut Sakhon', 'Samut Songkhram', 'Saraburi', 'Satun', 'Sing Buri', 'Si Sa Ket', 'Songkhla', 'Sukhothai', 'Suphan Buri', 'Surat Thani', 'Surin', 'Tak', 'Trang', 'Trat', 'Ubon Ratchathani', 'Udon Thani', 'Uthai Thani', 'Uttaradit', 'Yala', 'Yasothon',
            ]),
        ];
    }

    public function forSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'sale',
            'price' => fake()->randomFloat(2, 500, 50000),
        ]);
    }

    public function forAdoption(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'adoption',
            'price' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => fake()->randomElement(['reserved', 'sold', 'closed']),
        ]);
    }
}
