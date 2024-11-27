<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:50|unique:companies,registration_number',
            'foundation_date' => 'nullable|date',
            'activity' => 'nullable|string|max:100',
            'active' => 'nullable|boolean',
        ];
    }
}
