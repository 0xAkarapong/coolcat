<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCatInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('inquiry'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:confirmed,rejected,completed,cancelled'],
            'seller_note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
