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
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'registration_number' => $this->resource->registration_number,
            'foundation_date' => $this->resource->foundation_date,
            'activity' => $this->resource->activity,
            'active' => $this->resource->active,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,

            'addresses' => $this->whenLoaded('address'),
            'employees' => $this->whenLoaded('employee'),
            'owners' => $this->whenLoaded('owner'),
        ];
    }
}
