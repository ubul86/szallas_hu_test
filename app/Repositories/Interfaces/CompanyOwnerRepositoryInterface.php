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
    public function index(int $companyId, array $filters = []): LengthAwarePaginator;
    public function show(int $companyId, int $id): CompanyOwner;
    public function store(int $companyId, array $data): CompanyOwner;
    public function update(int $companyId, int $id, array $data): CompanyOwner;
    public function destroy(int $companyId, int $id): bool|null;
    public function validateOwnership(int $companyId, int $ownerId): void;
}
