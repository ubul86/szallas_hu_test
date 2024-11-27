<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\Table;
use Exception;

class CompanyFoundationDatePaginatorAlternative extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:foundation-date-paginator-with-recursive-cte {--page=1}';

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

        $perPage = 50;

        $offset = $page < 1 ? 0 : ($page - 1) * $perPage;

        try {
            $results = DB::select(
                "
                    WITH RECURSIVE date_range AS (
                        SELECT DATE('2001-01-01') AS date
                        UNION ALL
                        SELECT DATE_ADD(date, INTERVAL 1 DAY)
                        FROM date_range
                        WHERE date < NOW()
                        LIMIT :perPage OFFSET :offset
                    )
                    SELECT
                        date_range.date AS company_foundation_date,
                        name
                    FROM
                        date_range
                    LEFT JOIN
                        companies
                    ON
                        companies.foundation_date = date_range.date
                    ORDER BY
                        date_range.date ASC;
        ",
                ['perPage' => $perPage, 'offset' => $offset]
            );
        }
        catch(Exception $e) {
            $this->error('You need to increase the max recursion depth to 10000: SET GLOBAL cte_max_recursion_depth=10000;');
        }


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
