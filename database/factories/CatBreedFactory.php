<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CatBreed>
 */
class CatBreedFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $breeds = [
            ['name' => 'Persian', 'name_th' => 'เปอร์เซีย', 'origin' => 'Iran'],
            ['name' => 'Scottish Fold', 'name_th' => 'สก็อตติช โฟลด์', 'origin' => 'Scotland'],
            ['name' => 'British Shorthair', 'name_th' => 'บริติช ชอร์ตแฮร์', 'origin' => 'UK'],
            ['name' => 'Siamese', 'name_th' => 'วิเชียรมาศ', 'origin' => 'Thailand'],
            ['name' => 'Maine Coon', 'name_th' => 'เมนคูน', 'origin' => 'USA'],
            ['name' => 'Ragdoll', 'name_th' => 'แร็กดอลล์', 'origin' => 'USA'],
            ['name' => 'Bengal', 'name_th' => 'เบงกอล', 'origin' => 'USA'],
            ['name' => 'Sphynx', 'name_th' => 'สฟิงซ์', 'origin' => 'Canada'],
            ['name' => 'Domestic Shorthair', 'name_th' => 'แมวบ้าน', 'origin' => 'Mixed'],
        ];

        $breed = fake()->randomElement($breeds);

        return [
            'name' => $breed['name'],
            'name_th' => $breed['name_th'],
            'origin' => $breed['origin'],
            'image' => null,
        ];
    }
}
