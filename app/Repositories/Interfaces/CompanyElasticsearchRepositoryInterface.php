<?php

namespace App\Repositories\Interfaces;

use App\Models\Company;
use Illuminate\Pagination\LengthAwarePaginator;
use Http\Promise\Promise;
use Elastic\Elasticsearch\Response\Elasticsearch as ElasticsearchResponse;

interface CompanyElasticsearchRepositoryInterface
{
    /**
     * @param array $filters
     * @return LengthAwarePaginator<Company>
     */
    public function index(array $filters = []): LengthAwarePaginator;

    public function search(array $filters): array;

    public function show(int $id): array;

    public function store(Company $company): void;

    public function update(Company $company): void;

    public function destroy(int $id): void;

    public function createIndexIfNeeded(): void;

    public function getElasticRecords(): array;
}
