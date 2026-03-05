<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'message' => ['nullable', 'string', 'max:1000'],
            'meet_date' => ['nullable', 'date', 'after:today'],
            'meet_time' => ['nullable', 'date_format:H:i'],
            'meet_location' => ['nullable', 'string', 'max:255'],
        ];
    }
}
