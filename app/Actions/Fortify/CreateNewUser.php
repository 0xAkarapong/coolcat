<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        $supabaseUrl = config('services.supabase.url');
        $supabaseAnonKey = config('services.supabase.anon_key');

        if (! $supabaseUrl || ! $supabaseAnonKey) {
            throw ValidationException::withMessages([
                'email' => __('Supabase credentials are not configured.'),
            ]);
        }

        // 1. Sign up the user via Supabase GoTrue API
        $response = Http::withHeaders([
            'apikey' => $supabaseAnonKey,
            'Authorization' => 'Bearer '.$supabaseAnonKey,
        ])->post($supabaseUrl.'/auth/v1/signup', [
            'email' => $input['email'],
            'password' => $input['password'],
            'data' => [
                'name' => $input['name'],
            ],
        ]);

        if ($response->failed()) {
            $error = $response->json('msg') ?? $response->json('error_description') ?? __('Registration failed via Supabase.');
            throw ValidationException::withMessages([
                'email' => $error,
            ]);
        }

        $supabaseData = $response->json();
        $supabaseUserId = $supabaseData['user']['id'] ?? $supabaseData['id'] ?? null;

        // 2. Create the proxy user in the local database
        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'supabase_id' => $supabaseUserId,
        ]);
    }
}
