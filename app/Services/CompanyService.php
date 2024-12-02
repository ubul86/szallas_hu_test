<?php

namespace App\Services;

use App\Models\Company;
use App\Repositories\Interfaces\CompanyElasticsearchRepositoryInterface;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

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
        return $this->companyRepository->store($data);
    }

    public function storeWithRelations(array $data): Company
    {
        $company = $this->companyRepository->storeWithRelations($data);
        return $company;
    }

    public function update(int $id, array $data): Company
    {
        return $this->companyRepository->update($id, $data);
    }

    public function destroy(int $id): bool|null
    {
        return $this->companyRepository->destroy($id);
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
