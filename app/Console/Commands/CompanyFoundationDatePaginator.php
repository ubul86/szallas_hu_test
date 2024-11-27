<?php

namespace App\Console\Commands;

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
    protected $signature = 'company:foundation-date-paginator {--page=1}';

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

        $results = DB::select(
            "
                    SELECT
                        gen_date as company_foundation_date,
                        c.name AS company_name
                    FROM
                        (
                            SELECT adddate('2001-01-01', t4*10000 + t3*1000 + t2*100 + t1*10 + t0) AS gen_date
                            FROM
                                (SELECT 0 t0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
                                (SELECT 0 t1 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
                                (SELECT 0 t2 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
                                (SELECT 0 t3 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3
                        ) AS v
                    LEFT JOIN companies c ON c.foundation_date = v.gen_date
                    WHERE v.gen_date <= NOW()
                    ORDER BY v.gen_date ASC
                    LIMIT :perPage OFFSET :offset;
        ",
            ['perPage' => $perPage, 'offset' => $offset]
        );

        if (empty($results)) {
            $this->info('No results found for the given page.');
            return;
        }

        $rows = array_map(function ($item) {
            return [
                'Foundation Date' => $item->company_foundation_date,
                'Company Name' => $item->company_name,
            ];
        }, $results);

        $table = new Table($this->output);
        $table->setHeaders(['Foundation Date', 'Company Name']);
        $table->setRows($rows);
        $table->render();
    }
}
