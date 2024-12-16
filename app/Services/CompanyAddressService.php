<?php

namespace App\Services;

use App\Models\CompanyAddress;
use App\Repositories\Interfaces\CompanyElasticsearchRepositoryInterface;
use App\Repositories\Interfaces\CompanyAddressRepositoryInterface;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CompanyAddressService
{
    protected CompanyRepositoryInterface $companyRepository;
    protected CompanyAddressRepositoryInterface $companyAddressRepository;
    protected CompanyElasticsearchRepositoryInterface $companyElasticsearchRepository;

    public function __construct(
        CompanyRepositoryInterface $companyRepository,
        CompanyAddressRepositoryInterface $companyAddressRepository,
        CompanyElasticsearchRepositoryInterface $companyElasticsearchRepository
    ) {
        $this->companyRepository = $companyRepository;
        $this->companyAddressRepository = $companyAddressRepository;
        $this->companyElasticsearchRepository = $companyElasticsearchRepository;
    }

    /**
     * @param array $filters
     * @return LengthAwarePaginator<CompanyAddress>
     */
    public function index(int $companyId, array $filters): LengthAwarePaginator
    {
        return $this->companyAddressRepository->index($companyId, $filters);
    }

    public function store(int $companyId, array $data): CompanyAddress
    {
        return DB::transaction(function () use ($companyId, $data) {
            $companyAddress = $this->companyAddressRepository->store($companyId, $data);
            try {
                $company = $this->companyRepository->findById($companyId);
                $this->companyElasticsearchRepository->update($company);
            } catch (\Exception $e) {
                throw $e;
            }

            return $companyAddress;
        });
    }

    public function update(int $companyId, int $id, array $data): CompanyAddress
    {
        return DB::transaction(function () use ($companyId, $id, $data) {
            $companyAddress = $this->companyAddressRepository->update($companyId, $id, $data);
            try {
                $company = $this->companyRepository->findById($companyId);
                $this->companyElasticsearchRepository->update($company);
            } catch (\Exception $e) {
                throw $e;
            }

            return $companyAddress;
        });
    }

    public function destroy(int $companyId, int $id): bool|null
    {
        DB::transaction(function () use ($companyId, $id) {
            $this->companyAddressRepository->destroy($companyId, $id);

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

    public function show(int $companyId, int $id): CompanyAddress
    {
        return $this->companyAddressRepository->show($companyId, $id);
    }
}
