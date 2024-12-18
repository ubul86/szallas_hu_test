<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Company;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var string|int|null $companyId */
        $companyId = $this->route('company');
        $company = Company::findOrFail($companyId);

        return [
            'name' => 'required|string|max:255',
            'registration_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('companies')->ignore($companyId),
            ],
            'foundation_date' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($company) {
                    if ($value && $value !== $company->foundation_date) {
                        $fail('Company Foundation Date Cant modified.');
                    }
                }
            ],
            'activity' => 'nullable|string|max:100',
            'active' => 'nullable|boolean',
        ];
    }
}
