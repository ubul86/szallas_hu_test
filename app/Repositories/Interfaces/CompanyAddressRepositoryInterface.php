<?php

namespace App\Repositories\Interfaces;

use App\Models\CompanyAddress;
use Illuminate\Pagination\LengthAwarePaginator;

interface CompanyAddressRepositoryInterface
{
    /**
     * @param array $filters
     * @return LengthAwarePaginator<CompanyAddress>
     */
    public function index(array $filters = []): LengthAwarePaginator;
    public function show(int $id): CompanyAddress;
    public function store(array $data, int $companyId = null): CompanyAddress;
    public function update(int $id, array $data): CompanyAddress;
    public function destroy(int $id): bool|null;
}
