<?php

namespace App\Jobs;

use Elastic\Elasticsearch\Client as ElasticsearchClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class BulkDeleteCompaniesJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    protected array $companyIds;

    public function __construct(array $companyIds)
    {
        $this->companyIds = $companyIds;
    }

    public function handle(ElasticsearchClient $elasticsearch): void
    {
        $params = ['body' => []];
        foreach ($this->companyIds as $id) {
            $params['body'][] = [
                'delete' => [
                    '_index' => 'companies',
                    '_id' => $id,
                ],
            ];
        }

        if (!empty($params['body'])) {
            $elasticsearch->bulk($params);
        }
    }
}
