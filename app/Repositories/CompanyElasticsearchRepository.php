<?php

namespace App\Repositories;

use App\Models\Company;
use App\Repositories\Interfaces\CompanyElasticsearchRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;
use Elastic\Elasticsearch\Client as ElasticClient;
use Http\Promise\Promise;
use Elastic\Elasticsearch\Response\Elasticsearch as ElasticsearchResponse;
use Illuminate\Support\Collection;

class CompanyElasticsearchRepository implements CompanyElasticsearchRepositoryInterface
{
    protected ElasticClient $elasticsearch;

    public function __construct(ElasticClient $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function search(array $filters): ElasticsearchResponse|Promise
    {
        // TODO: Implement search() method.
    }

    public function index(array $filters = []): LengthAwarePaginator
    {

        $perPage = $filters['itemsPerPage'] ?? 10;
        $page = $filters['page'] ?? 1;
        $from = ($page - 1) * $perPage;

        $response = $this->elasticsearch->search([
            'index' => 'companies',
            'body' => [
                'from' => $from,
                'size' => $perPage,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['match_all' => (object)[]]
                        ]
                    ]
                ]
            ]
        ]);

        $resultArray = $response->asArray();
        $companies = collect($resultArray['hits']['hits'])->map(function ($hit) {
            return $hit['_source'];
        });

        $total = $resultArray['hits']['total']['value'];

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

    public function show(int $id): ElasticsearchResponse|Promise
    {
        return $this->elasticsearch->get([
            'index' => 'companies',
            'id' => $id
        ]);
    }

    public function store(array $data): Company
    {
        // TODO: Implement store() method.
    }

    public function update(int $id, array $data): Company
    {
        // TODO: Implement update() method.
    }

    public function destroy(int $id): ElasticsearchResponse|Promise
    {
        return $this->elasticsearch->delete([
            'index' => 'companies',
            'id' => $id
        ]);
    }

    public function createIndexIfNeeded(): void
    {
        $indexExists = $this->elasticsearch->indices()->exists([
            'index' => 'companies',
        ]);

        if ($indexExists->getStatusCode() !== 200) {
            $this->elasticsearch->indices()->create([
                'index' => 'companies',
                'body'  => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0
                    ],
                    'mappings' => [
                        'properties' => [
                            'name' => ['type' => 'text'],
                            'location' => ['type' => 'text'],
                        ]
                    ]
                ]
            ]);
        }
    }
}
