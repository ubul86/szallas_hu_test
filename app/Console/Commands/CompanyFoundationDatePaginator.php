<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\Table;

class CompanyFoundationDatePaginator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:foundation-date-paginator {--page=1} {--date-start=2001-01-01} {--date-end=} {--per-page=50}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Returns all days from January 1, 2001, indicating the creation of companies on each day.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $page = (int) $this->option('page');
        $dateStart = $this->option('date-start');
        $dateEnd = $this->option('date-end');
        $perPage = $this->option('per-page');

        $results = DB::select(
            "CALL GenerateDateRangeWithCompanyNames(:dateStart, :dateEnd, :page, :perPage)",
            [
                'dateStart' => $dateStart ?? '2001-01-01',
                'dateEnd' => $dateEnd ?? Carbon::now()->format('Y-m-d'),
                'page' => $page ?? 1,
                'perPage' => $perPage ?? 50
            ]
        );

        if (empty($results)) {
            $this->info('No results found for the given page.');
            return;
        }

        $rows = array_map(function ($item) {
            return [
                'Foundation Date' => $item->company_foundation_date,
                'Company Name' => $item->name,
            ];
        }, $results);

        $table = new Table($this->output);
        $table->setHeaders(['Foundation Date', 'Company Name']);
        $table->setRows($rows);
        $table->render();
    }
}
