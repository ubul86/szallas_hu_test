<?php

namespace App\Repositories;

use App\Infrastructure\Elasticsearch\ElasticsearchQueryBuilder;
use App\Models\Company;
use App\Repositories\Interfaces\CompanyElasticsearchRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CompanyElasticsearchRepository implements CompanyElasticsearchRepositoryInterface
{
    protected ElasticsearchQueryBuilder $queryBuilder;

    public function __construct(ElasticsearchQueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function search(array $filters): array
    {
        $query = $this->queryBuilder
            ->index('companies');

        if (!empty($filters['name'])) {
            $query->where('name', $filters['name']);
        }

        if (!empty($filters['location'])) {
            $query->where('location', $filters['location']);
        }

        return $query->execute();
    }

    /**
     * @param array $filters
     * @return LengthAwarePaginator<Company>
     */
    public function index(array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['itemsPerPage'] ?? 10;
        $page = $filters['page'] ?? 1;
        $from = ($page - 1) * $perPage;

        $this->queryBuilder->index('companies');

        $mustConditions = [];
        $filterConditions = [];

        if (!empty($filters['search'])) {
            $mustConditions[] = [
                'wildcard' => [
                    'name' => [
                        'value' => '*' . $filters['search'] . '*',
                        'boost' => 1.0,
                        'rewrite' => 'constant_score'
                    ]
                ]
            ];
        }

        if (!empty($filters['filters']) && is_array($filters['filters'])) {
            foreach ($filters['filters'] as $field => $values) {
                if (!is_array($values)) {
                    $values = [$values];
                }

                if ($field === 'active') {
                    $values = array_map(fn($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE), $values);
                }

                $filterConditions[] = [
                    'terms' => [
                        $field => $values
                    ]
                ];
            }
        }

        if (!empty($filters['ids'])) {
            $ids = explode(',', $filters['ids']);
            $mustConditions[] = [
                'terms' => [
                    '_id' => $ids
                ]
            ];
        }

        if (empty($mustConditions) && empty($filterConditions)) {
            $this->queryBuilder->body([
                'query' => [
                    'match_all' => new \stdClass()
                ]
            ]);
        } else {
            $this->queryBuilder->body([
                'query' => [
                    'bool' => [
                        'must' => $mustConditions,
                        'filter' => $filterConditions
                    ]
                ]
            ]);
        }

        $this->queryBuilder
            ->from($from)
            ->size($perPage);

        if (!empty($filters['sortBy']) && is_array($filters['sortBy'])) {
            foreach ($filters['sortBy'] as $sort) {
                if (!empty($sort['key']) && !empty($sort['order'])) {
                    $this->queryBuilder->sort($sort['key'], $sort['order']);
                }
            }
        }

        $response = $this->queryBuilder->execute();

        $companies = (new Collection($response['hits']))->map(fn($hit) => $hit['_source']);
        $total = $response['total']['value'];

        return new LengthAwarePaginator(
            $companies,
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query()
            ]
        );
    }

    public function show(int $id): array
    {
        return $this->queryBuilder
            ->index('companies')
            ->term('_id', (string) $id)
            ->execute();
    }

    public function store(Company $company): void
    {
        $data = $company->toArray();

        $this->queryBuilder
            ->index('companies')
            ->body($data)
            ->save();
    }

    public function update(Company $company): void
    {
        $this->queryBuilder
            ->index('companies')
            ->id($company->id)
            ->body($company->toArray())
            ->save();
    }

    public function destroy(int $id): void
    {
        $this->queryBuilder
            ->index('companies')
            ->id($id)
            ->delete();
    }

    public function createIndexIfNeeded(): void
    {
        if (!$this->queryBuilder->index('companies')->indexExists()) {
            $this->queryBuilder
                ->index('companies')
                ->body([
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                    ],
                    'mappings' => [
                        'properties' => [
                            'name' => ['type' => 'text'],
                            'location' => ['type' => 'text'],
                            'active' => ['type' => 'boolean'],
                        ],
                    ],
                ])
                ->execute();
        }
    }

    public function getElasticRecords(): array
    {
        $hits = $this->queryBuilder
            ->index('companies')
            ->size(10000)
            ->body([
                '_source' => ['_id', 'updated_at'],
            ])
            ->search();

        return collect($hits)->mapWithKeys(function ($hit) {
            return [$hit['_id'] => $hit['_source']['updated_at']];
        })->toArray();
    }
}
