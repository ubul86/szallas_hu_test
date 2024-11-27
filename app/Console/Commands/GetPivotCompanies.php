<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\Table;

class GetPivotCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:generate-pivot-companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {

        $results = DB::select('CALL GetPivotCompanies()');

        if (empty($results)) {
            $this->info('No results found for the given page.');
            return;
        }

        $headersCollection = new Collection($results[0]);

        $headers = $headersCollection->keys()->toArray();

        $rows = [];
        foreach ($results as $entry) {
            $collectedRow = new Collection($entry);
            $rows[] = $collectedRow->values()->toArray();
        }

        $table = new Table($this->output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->render();
    }
}
