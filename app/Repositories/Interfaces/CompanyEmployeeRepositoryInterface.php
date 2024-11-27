<?php

namespace App\Repositories\Interfaces;

use App\Models\CompanyEmployee;
use Illuminate\Pagination\LengthAwarePaginator;

interface CompanyEmployeeRepositoryInterface
{
    /**
     * @param array $filters
     * @return LengthAwarePaginator<CompanyEmployee>
     */
    public function index(array $filters = []): LengthAwarePaginator;
    public function show(int $id): CompanyEmployee;
    public function store(array $data, int $companyId = null): CompanyEmployee;
    public function update(int $id, array $data): CompanyEmployee;
    public function destroy(int $id): bool|null;
}
