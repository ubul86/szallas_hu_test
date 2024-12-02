<?php

namespace App\Repositories\Interfaces;

use App\Models\Company;
use Illuminate\Pagination\LengthAwarePaginator;
use Http\Promise\Promise;
use Elastic\Elasticsearch\Response\Elasticsearch as ElasticsearchResponse;

interface CompanyElasticsearchRepositoryInterface
{
    public function index(array $filters = []): LengthAwarePaginator;

    public function search(array $filters): ElasticsearchResponse|Promise;

    public function show(int $id): ElasticsearchResponse|Promise;

    public function store(Company $company): void;

    public function update(Company $company): void;

    public function destroy(int $id): void;

    public function createIndexIfNeeded(): void;
}
