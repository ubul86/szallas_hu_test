<?php

namespace App\Formatters;

class ElasticsearchFormatter
{
    public static function format(array $data): array
    {
        $collection = collect($data);

        return [
            'id' => $collection->get('id'),
            'name' => $collection->get('name'),
            'registration_number' => $collection->get('registration_number'),
            'foundation_date' => $collection->get('foundation_date'),
            'activity' => $collection->get('activity'),
            'active' => $collection->get('active'),
            'created_at' => $collection->get('created_at'),
            'updated_at' => $collection->get('updated_at'),
            'addresses' => $collection->get('address', []),
            'employees' => $collection->get('employee', []),
            'owners' => $collection->get('owner', []),
        ];
    }
}
