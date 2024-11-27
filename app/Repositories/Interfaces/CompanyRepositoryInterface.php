<?php

namespace App\Repositories\Interfaces;

use App\Models\Company;
use Illuminate\Pagination\LengthAwarePaginator;

interface CompanyRepositoryInterface
{
    /**
     * @param array $filters
     * @return LengthAwarePaginator<Company>
     */
    public function index(array $filters = []): LengthAwarePaginator;
    public function show(int $id): Company;
    public function store(array $data): Company;
    public function update(int $id, array $data): Company;
    public function destroy(int $id): bool|null;
}
