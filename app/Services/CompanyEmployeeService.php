<?php

namespace App\Services;

use App\Models\CompanyEmployee;
use App\Repositories\Interfaces\CompanyElasticsearchRepositoryInterface;
use App\Repositories\Interfaces\CompanyEmployeeRepositoryInterface;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CompanyEmployeeService
{
    protected CompanyRepositoryInterface $companyRepository;
    protected CompanyEmployeeRepositoryInterface $companyEmployeeRepository;
    protected CompanyElasticsearchRepositoryInterface $companyElasticsearchRepository;

    public function __construct(
        CompanyRepositoryInterface $companyRepository,
        CompanyEmployeeRepositoryInterface $companyEmployeeRepository,
        CompanyElasticsearchRepositoryInterface $companyElasticsearchRepository
    ) {
        $this->companyRepository = $companyRepository;
        $this->companyEmployeeRepository = $companyEmployeeRepository;
        $this->companyElasticsearchRepository = $companyElasticsearchRepository;
    }

    /**
     * @param array $filters
     * @return LengthAwarePaginator<CompanyEmployee>
     */
    public function index(int $companyId, array $filters): LengthAwarePaginator
    {
        return $this->companyEmployeeRepository->index($companyId, $filters);
    }

    public function store(int $companyId, array $data): CompanyEmployee
    {
        return DB::transaction(function () use ($companyId, $data) {
            $companyEmployee = $this->companyEmployeeRepository->store($companyId, $data);
            try {
                $company = $this->companyRepository->findById($companyId);
                $this->companyElasticsearchRepository->update($company);
            } catch (\Exception $e) {
                throw $e;
            }

            return $companyEmployee;
        });
    }

    public function update(int $companyId, int $id, array $data): CompanyEmployee
    {
        return DB::transaction(function () use ($companyId, $id, $data) {
            $companyEmployee = $this->companyEmployeeRepository->update($companyId, $id, $data);
            try {
                $company = $this->companyRepository->findById($companyId);
                $this->companyElasticsearchRepository->update($company);
            } catch (\Exception $e) {
                throw $e;
            }

            return $companyEmployee;
        });
    }

    public function destroy(int $companyId, int $id): bool|null
    {
        DB::transaction(function () use ($companyId, $id) {
            $this->companyEmployeeRepository->destroy($companyId, $id);

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

    public function show(int $companyId, int $id): CompanyEmployee
    {
        return $this->companyEmployeeRepository->show($companyId, $id);
    }

    public function validateOwnership(int $companyId, int $employeeId): void
    {
        $this->companyEmployeeRepository->validateOwnership($companyId, $employeeId);
    }
}
