<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'breed_id' => ['nullable', 'exists:cat_breeds,id'],
            'gender' => ['required', 'in:male,female,unknown'],
            'birthdate' => ['nullable', 'date', 'before:today'],
            'color' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'max:2048'],
            'type' => ['required', 'in:adoption,sale'],
            'price' => ['nullable', 'numeric', 'min:0', 'required_if:type,sale'],
            'is_neutered' => ['boolean'],
            'is_vaccinated' => ['boolean'],
            'province' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'price.required_if' => 'A price is required for listings of type "sale".',
        ];
    }
}
