<?php

namespace App\Repositories;

use App\Models\Company;
use App\Repositories\Interfaces\CompanyElasticsearchRepositoryInterface;
use App\Services\ElasticsearchQueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function index(array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['itemsPerPage'] ?? 10;
        $page = $filters['page'] ?? 1;
        $from = ($page - 1) * $perPage;

        $response = $this->queryBuilder
            ->index('companies')
            ->matchAll()
            ->from($from)
            ->size($perPage)
            ->execute();

        $companies = collect($response['hits']['hits'])->map(fn($hit) => $hit['_source']);
        $total = $response['hits']['total']['value'];

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
            ->term('_id', $id)
            ->execute();
    }

    public function store(Company $company): void
    {
        $company->active = (int) $company->active;

        $this->queryBuilder
            ->index('companies')
            ->body($company->toArray())
            ->save();
    }

    public function update(Company $company): void
    {
        $this->queryBuilder
            ->index('companies')
            ->id($company->id)
            ->body([
                'doc' => $company->toArray(),
            ])
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
                        ],
                    ],
                ])
                ->execute();
        }
    }
}
