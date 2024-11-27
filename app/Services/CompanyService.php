<?php

namespace App\Services;

use App\Models\Company;
use App\Repositories\CompanyAddressRepository;
use App\Repositories\CompanyEmployeeRepository;
use App\Repositories\CompanyOwnerRepository;
use App\Repositories\CompanyRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CompanyService
{
    protected CompanyRepository $companyRepository;
    protected CompanyAddressRepository $companyAddressRepository;
    protected CompanyEmployeeRepository $companyEmployeeRepository;
    protected CompanyOwnerRepository $companyOwnerRepository;

    public function __construct(
        CompanyRepository $companyRepository,
        CompanyAddressRepository $companyAddressRepository,
        CompanyEmployeeRepository $companyEmployeeRepository,
        CompanyOwnerRepository $companyOwnerRepository
    ) {
        $this->companyRepository = $companyRepository;
        $this->companyAddressRepository = $companyAddressRepository;
        $this->companyEmployeeRepository = $companyEmployeeRepository;
        $this->companyOwnerRepository = $companyOwnerRepository;
    }

    /**
     * @param array $filters
     * @return LengthAwarePaginator<Company>
     */
    public function index(array $filters): LengthAwarePaginator
    {
        return $this->companyRepository->index($filters);
    }

    public function store(array $data): Company
    {
        return $this->companyRepository->store($data);
    }

    public function storeWithRelations(array $data): Company
    {
        DB::beginTransaction();

        $collectedData = collect($data);

        try {
            $company = $this->companyRepository->store($collectedData->get('company', []));

            if ($collectedData->has('address')) {
                foreach ($collectedData->get('address', []) as $address) {
                    $this->companyAddressRepository->store($address, $company->id);
                }
            }

            if ($collectedData->has('employee')) {
                foreach ($collectedData->get('employee', []) as $employee) {
                    $this->companyEmployeeRepository->store($employee, $company->id);
                }
            }

            if ($collectedData->has('owner')) {
                foreach ($collectedData->get('owner', []) as $owner) {
                    $this->companyOwnerRepository->store($owner, $company->id);
                }
            }

            DB::commit();

            return $company;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
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
}
