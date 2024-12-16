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
    public function index(int $companyId, array $filters = []): LengthAwarePaginator;
    public function show(int $companyId, int $id): CompanyAddress;
    public function store(int $companyId, array $data): CompanyAddress;
    public function update(int $companyId, int $id, array $data): CompanyAddress;
    public function destroy(int $companyId, int $id): bool|null;
    public function validateOwnership(int $companyId, int $addressId): void;
}
