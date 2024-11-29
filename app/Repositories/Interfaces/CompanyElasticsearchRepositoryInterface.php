<?php

namespace App\Repositories\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;

interface CompanyElasticsearchRepositoryInterface extends CompanyRepositoryInterface
{
    public function search(array $filters): LengthAwarePaginator;
}
