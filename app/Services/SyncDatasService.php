<?php

namespace App\Services;

use App\Models\Company;
use Elastic\Elasticsearch\Client as ElasticsearchClient;

class SyncDatasService
{
    protected ElasticsearchClient $elasticsearch;

    public function __construct(ElasticsearchClient $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function getElasticRecords(): array
    {
        $response = $this->elasticsearch->search([
            'index' => 'companies',
            'size' => 10000,
            '_source' => ['_id', 'updated_at'],
        ]);

        return collect($response['hits']['hits'])->mapWithKeys(function ($hit) {
            return [$hit['_id'] => $hit['_source']['updated_at']];
        })->toArray();
    }

    public function getMysqlRecords(): array
    {
        return Company::pluck('updated_at', 'id')->toArray();
    }

    public function getRecordsToDelete(array $elasticIds, array $mysqlIds): array
    {
        return array_diff($elasticIds, $mysqlIds);
    }

    public function getRecordsToIndex(array $elasticIds, array $mysqlIds): array
    {
        return array_diff($mysqlIds, $elasticIds);
    }

    public function getRecordsToSync(array $mysqlCompanies, array $elasticRecords): array
    {
        $toDataSync = [];
        foreach ($mysqlCompanies as $id => $mysqlUpdatedAt) {
            if (isset($elasticRecords[$id]) && $elasticRecords[$id] !== $mysqlUpdatedAt) {
                $toDataSync[] = $id;
            }
        }

        return $toDataSync;
    }
}
