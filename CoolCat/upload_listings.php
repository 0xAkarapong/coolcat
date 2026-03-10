<?php

use App\Models\CatListing;
use App\Models\User;
use App\Models\CatBreed;
use Illuminate\Support\Facades\DB;

$filename = 'cat_listings_fake_data.csv';

if (!file_exists($filename)) {
    echo "File $filename not found.\n";
    exit(1);
}

// ensure we have at least one user and one breed
$user = User::first() ?? User::factory()->create();
$breed = CatBreed::first() ?? CatBreed::factory()->create();

$file = fopen($filename, 'r');
$headers = fgetcsv($file, 0, ',', '"', '\\');

$count = 0;
DB::beginTransaction();
try {
    while ($row = fgetcsv($file, 0, ',', '"', '\\')) {
        $data = array_combine($headers, $row);
        
        $userId = !empty($data['user_id']) && User::find($data['user_id']) ? $data['user_id'] : $user->id;
        $breedId = !empty($data['breed_id']) && CatBreed::find($data['breed_id']) ? $data['breed_id'] : $breed->id;

        CatListing::create([
            'user_id' => $userId,
            'breed_id' => $breedId,
            'name' => $data['name'],
            'gender' => $data['gender'],
            'birthdate' => $data['birthdate'],
            'color' => $data['color'],
            'description' => $data['description'],
            'image' => null, // Ignoring the fake image field string to stay clean
            'type' => $data['type'],
            'price' => $data['price'] !== '' ? $data['price'] : null,
            'status' => $data['status'],
            'is_neutered' => filter_var($data['is_neutered'], FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false',
            'is_vaccinated' => filter_var($data['is_vaccinated'], FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false',
            'views' => $data['views'],
            'province' => $data['province'],
        ]);
        $count++;
    }
    DB::commit();
    echo "Successfully uploaded $count listings into the database!\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error uploading listings: " . $e->getMessage() . "\n";
}

fclose($file);
