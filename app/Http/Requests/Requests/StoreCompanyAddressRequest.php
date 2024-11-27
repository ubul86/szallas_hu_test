<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country' => 'string|max:100',
            'city' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'street_address' => 'nullable|string|max:255',
            'latitude' => 'nullable|float',
            'longitude' => 'nullable|float',
        ];
    }
}
