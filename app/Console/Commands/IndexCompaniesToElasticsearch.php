<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Jobs\BulkIndexCompaniesJob;

class IndexCompaniesToElasticsearch extends Command
{
    protected $signature = 'elasticsearch:index-companies';
    protected $description = 'Index updated companies into Elasticsearch';

    public function handle(): void
    {
        $companies = Company::where('updated_at', '>', now()->subDay())->withRelations()->get()->chunk(10);

        foreach ($companies as $chunk) {
            BulkIndexCompaniesJob::dispatch($chunk);
        }

        $this->info('Indexing jobs dispatched.');
    }
}
