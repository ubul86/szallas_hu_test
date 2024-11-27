<?php

namespace App\Repositories\Interfaces;

use App\Models\CompanyOwner;
use Illuminate\Pagination\LengthAwarePaginator;

interface CompanyOwnerRepositoryInterface
{
    /**
     * @param array $filters
     * @return LengthAwarePaginator<CompanyOwner>
     */
    public function index(array $filters = []): LengthAwarePaginator;
    public function show(int $id): CompanyOwner;
    public function store(array $data, int $companyId = null): CompanyOwner;
    public function update(int $id, array $data): CompanyOwner;
    public function destroy(int $id): bool|null;
}
