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
    public function index(int $companyId, array $filters = []): LengthAwarePaginator;
    public function show(int $companyId, int $id): CompanyEmployee;
    public function store(int $companyId, array $data): CompanyEmployee;
    public function update(int $companyId, int $id, array $data): CompanyEmployee;
    public function destroy(int $companyId, int $id): bool|null;
    public function validateOwnership(int $companyId, int $employeeId): void;
}
