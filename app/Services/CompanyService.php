<?php

namespace App\Services;

use App\Models\Company;
use App\Repositories\Interfaces\CompanyElasticsearchRepositoryInterface;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CompanyService
{
    protected CompanyRepositoryInterface $companyRepository;
    protected CompanyElasticsearchRepositoryInterface $companyElasticsearchRepository;

    public function __construct(
        CompanyRepositoryInterface $companyRepository,
        CompanyElasticsearchRepositoryInterface $companyElasticsearchRepository
    ) {
        $this->companyRepository = $companyRepository;
        $this->companyElasticsearchRepository = $companyElasticsearchRepository;

        $this->createIndexIfNeeded();
    }

    /**
     * @param array $filters
     * @return LengthAwarePaginator<Company>
     */
    public function index(array $filters): LengthAwarePaginator
    {
        return $this->companyElasticsearchRepository->index($filters);
    }

    public function store(array $data): Company
    {
        return DB::transaction(function () use ($data) {
            $company = $this->companyRepository->store($data);
            try {
                $this->companyElasticsearchRepository->store($company);
            } catch (\Exception $e) {
                throw $e;
            }

            return $company;
        });
    }

    public function storeWithRelations(array $data): Company
    {
        $company = $this->companyRepository->storeWithRelations($data);
        return $company;
    }

    public function update(int $id, array $data): Company
    {
        return DB::transaction(function () use ($id, $data) {
            $company = $this->companyRepository->update($id, $data);

            try {
                $this->companyElasticsearchRepository->update($company);
            } catch (\Exception $e) {
                throw $e;
            }

            return $company;
        });
    }

    public function destroy(int $id): bool|null
    {
        DB::transaction(function () use ($id) {
            $company = $this->companyRepository->findById($id);

            $this->companyRepository->destroy($id);

            try {
                $this->companyElasticsearchRepository->destroy($company->id);
                return true;
            } catch (\Exception $e) {
                throw $e;
            }
        });
        return false;
    }

    public function show(int $id): Company
    {
        return $this->companyRepository->show($id);
    }

    public function checkExistsByRegistrationNumber(string $registrationNumber): bool
    {
        return $this->companyRepository->checkExistsByRegistrationNumber($registrationNumber);
    }

    protected function createIndexIfNeeded(): void
    {
        $this->companyElasticsearchRepository->createIndexIfNeeded();
    }
}
