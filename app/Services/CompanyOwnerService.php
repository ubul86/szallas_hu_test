<?php

namespace App\Services;

use App\Models\CompanyOwner;
use App\Repositories\Interfaces\CompanyElasticsearchRepositoryInterface;
use App\Repositories\Interfaces\CompanyOwnerRepositoryInterface;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CompanyOwnerService
{
    protected CompanyRepositoryInterface $companyRepository;
    protected CompanyOwnerRepositoryInterface $companyOwnerRepository;
    protected CompanyElasticsearchRepositoryInterface $companyElasticsearchRepository;

    public function __construct(
        CompanyRepositoryInterface $companyRepository,
        CompanyOwnerRepositoryInterface $companyOwnerRepository,
        CompanyElasticsearchRepositoryInterface $companyElasticsearchRepository
    ) {
        $this->companyRepository = $companyRepository;
        $this->companyOwnerRepository = $companyOwnerRepository;
        $this->companyElasticsearchRepository = $companyElasticsearchRepository;
    }

    /**
     * @param array $filters
     * @return LengthAwarePaginator<CompanyOwner>
     */
    public function index(int $companyId, array $filters): LengthAwarePaginator
    {
        return $this->companyOwnerRepository->index($companyId, $filters);
    }

    public function store(int $companyId, array $data): CompanyOwner
    {
        return DB::transaction(function () use ($companyId, $data) {
            $companyOwner = $this->companyOwnerRepository->store($companyId, $data);
            try {
                $company = $this->companyRepository->findById($companyId);
                $this->companyElasticsearchRepository->update($company);
            } catch (\Exception $e) {
                throw $e;
            }

            return $companyOwner;
        });
    }

    public function update(int $companyId, int $id, array $data): CompanyOwner
    {
        return DB::transaction(function () use ($companyId, $id, $data) {
            $companyOwner = $this->companyOwnerRepository->update($companyId, $id, $data);
            try {
                $company = $this->companyRepository->findById($companyId);
                $this->companyElasticsearchRepository->update($company);
            } catch (\Exception $e) {
                throw $e;
            }

            return $companyOwner;
        });
    }

    public function destroy(int $companyId, int $id): bool|null
    {
        DB::transaction(function () use ($companyId, $id) {
            $this->companyOwnerRepository->destroy($companyId, $id);

            try {
                $company = $this->companyRepository->findById($companyId);
                $this->companyElasticsearchRepository->update($company);
                return true;
            } catch (\Exception $e) {
                throw $e;
            }
        });
        return false;
    }

    public function show(int $companyId, int $id): CompanyOwner
    {
        return $this->companyOwnerRepository->show($companyId, $id);
    }

    public function validateOwnership(int $companyId, int $ownerId): void
    {
        $this->companyOwnerRepository->validateOwnership($companyId, $ownerId);
    }
}
