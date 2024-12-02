<?php

namespace App\Repositories\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;
use Http\Promise\Promise;
use \Elastic\Elasticsearch\Response\Elasticsearch as ElasticsearchResponse;
use Illuminate\Support\Collection;

interface CompanyElasticsearchRepositoryInterface
{
    public function index(array $filters = []): LengthAwarePaginator;

    public function search(array $filters): ElasticsearchResponse|Promise;

    public function show(int $id): ElasticsearchResponse|Promise;

    public function destroy(int $id): ElasticsearchResponse|Promise;

    public function createIndexIfNeeded(): void;
}
