<?php

namespace App\Formatters;

class ElasticsearchFormatter
{
    public static function format(array $data): array
    {
        return [
            'id' => $data['id'] ?? null,
            'name' => $data['name'] ?? null,
            'registration_number' => $data['registration_number'] ?? null,
            'foundation_date' => $data['foundation_date'] ?? null,
            'activity' => $data['activity'] ?? null,
            'active' => $data['active'] ?? null,
            'created_at' => $data['created_at'] ?? null,
            'updated_at' => $data['updated_at'] ?? null,
            'addresses' => $data['address'] ?? null,
            'employees' => $data['employee'] ?? null,
            'owners' => $data['owner'] ?? null,
        ];
    }
}
