<?php
namespace App\Jobs;

use Elastic\Elasticsearch\Client as ElasticsearchClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;

class BulkIndexCompaniesJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected Collection $companies;

    public function __construct(Collection $companies)
    {
        $this->companies = $companies;
    }

    public function handle(ElasticsearchClient $elasticsearch): void
    {
        $params = ['body' => []];
        foreach ($this->companies as $company) {
            $params['body'][] = [
                'index' => [
                    '_index' => 'companies',
                    '_id'    => $company->id,
                ]
            ];
            $params['body'][] = $company->toArray();
        }

        if (!empty($params['body'])) {
            $elasticsearch->bulk($params);
        }
    }
}
