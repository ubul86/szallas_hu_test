<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Company;

/**
 * @property Company $resource
 */
class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = collect($this->resource);

        return [
            'id' => $data->get('id'),
            'name' => $data->get('name'),
            'registration_number' => $data->get('registration_number'),
            'foundation_date' => $data->get('foundation_date'),
            'activity' => $data->get('activity'),
            'active' => $data->get('active'),
            'created_at' => $data->get('created_at'),
            'updated_at' => $data->get('updated_at'),

            'addresses' => $data->has('address') ? $data->get('address') : null,
            'employees' => $data->has('employee') ? $data->get('employee') : null,
            'owners' => $data->has('owner') ? $data->get('owner') : null,
        ];
    }
}
