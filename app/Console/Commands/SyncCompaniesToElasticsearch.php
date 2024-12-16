<?php

namespace App\Console\Commands;

use App\Jobs\BulkDeleteCompaniesJob;
use App\Services\SyncDatasService;
use Illuminate\Console\Command;
use App\Models\Company;
use App\Jobs\BulkIndexCompaniesJob;

class SyncCompaniesToElasticsearch extends Command
{
    protected $signature = 'elasticsearch:sync-companies';
    protected $description = 'Sync companies into Elasticsearch';

    protected SyncDatasService $syncDatasService;

    public function __construct(SyncDatasService $syncDatasService)
    {
        parent::__construct();
        $this->syncDatasService = $syncDatasService;
    }

    public function handle(): void
    {

        $mysqlCompanies = $this->syncDatasService->getMysqlRecords();
        $mysqlIds = array_keys($mysqlCompanies);

        $elasticRecords = $this->syncDatasService->getElasticRecords();
        $elasticIds = array_keys($elasticRecords);

        $toDelete = $this->syncDatasService->getRecordsToDelete($elasticIds, $mysqlIds);
        $toIndex = $this->syncDatasService->getRecordsToIndex($elasticIds, $mysqlIds);
        $toDataSync = $this->syncDatasService->getRecordsToSync($mysqlCompanies, $elasticRecords);

        if (!empty($toDelete)) {
            BulkDeleteCompaniesJob::dispatch($toDelete);
        }

        if (!empty($toIndex)) {
            $companies = Company::whereIn('id', $toIndex)->withRelations()->get()->chunk(100);
            foreach ($companies as $chunk) {
                BulkIndexCompaniesJob::dispatch($chunk);
            }
        }

        if (!empty($toDataSync)) {
            $companiesToSync = Company::where(function ($query) {
                $query->where('updated_at', '>', now()->subDay())
                    ->orWhereHas('address', function ($q) {
                        $q->where('updated_at', '>', now()->subDay());
                    })
                    ->orWhereHas('owner', function ($q) {
                        $q->where('updated_at', '>', now()->subDay());
                    })
                    ->orWhereHas('employee', function ($q) {
                        $q->where('updated_at', '>', now()->subDay());
                    });
            })->withRelations()->get()->chunk(100);
            foreach ($companiesToSync as $chunk) {
                BulkIndexCompaniesJob::dispatch($chunk);
            }
        }

        $this->info('Synchronization complete: ' . count($toDelete) . ' deleted, ' . count($toIndex) . ' new, ' . count($toDataSync) . ' updated.');
    }
}
