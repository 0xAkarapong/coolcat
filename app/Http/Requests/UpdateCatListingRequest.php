<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCatListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('listing'));
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'breed_id' => ['sometimes', 'nullable', 'exists:cat_breeds,id'],
            'gender' => ['sometimes', 'required', 'in:male,female,unknown'],
            'birthdate' => ['sometimes', 'nullable', 'date', 'before:today'],
            'color' => ['sometimes', 'nullable', 'string', 'max:50'],
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'image' => ['sometimes', 'nullable', 'image', 'max:2048'],
            'type' => ['sometimes', 'required', 'in:adoption,sale'],
            'price' => ['sometimes', 'nullable', 'numeric', 'min:0', 'required_if:type,sale'],
            'status' => ['sometimes', 'required', 'in:active,reserved,sold,closed'],
            'is_neutered' => ['sometimes', 'boolean'],
            'is_vaccinated' => ['sometimes', 'boolean'],
            'province' => ['sometimes', 'nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'price.required_if' => 'A price is required for listings of type "sale".',
        ];
    }
}
