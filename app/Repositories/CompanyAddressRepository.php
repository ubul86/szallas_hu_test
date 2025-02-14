<?php

namespace App\Repositories;

use App\Models\CompanyAddress;
use App\Repositories\Interfaces\CompanyAddressRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;

class CompanyAddressRepository implements CompanyAddressRepositoryInterface
{
    /**
     * @param array $filters
     * @return LengthAwarePaginator<CompanyAddress>
     */
    public function index(int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = CompanyAddress::with('company')->where('company_id', $companyId);

        $perPage = $filters['itemsPerPage'] ?? 10;
        $page = $filters['page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function show(int $companyId, int $id): CompanyAddress
    {
        $this->validateOwnership($companyId, $id);

        try {
            return CompanyAddress::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Company Address not found: ' . $e->getMessage());
        }
    }

    public function store(int $companyId, array $data): CompanyAddress
    {
        try {
            $address = new CompanyAddress();

            $address->fill($data);
            $address->company_id = $companyId;
            $address->save();

            return $address;
        } catch (Exception $e) {
            throw new Exception('Failed to create Company Address: ' . $e->getMessage());
        }
    }

    public function update(int $companyId, int $id, array $data): CompanyAddress
    {

        $this->validateOwnership($companyId, $id);

        try {
            $company = CompanyAddress::findOrFail($id);
            $company->update($data);
            return $company;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Company Address not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to update Company Address: ' . $e->getMessage());
        }
    }

    public function destroy(int $companyId, int $id): bool|null
    {

        $this->validateOwnership($companyId, $id);

        try {
            $company = CompanyAddress::findOrFail($id);
            return $company->delete();
        } catch (ModelNotFoundException $e) {
            throw new Exception('Company Address not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to delete Company Address: ' . $e->getMessage());
        }
    }

    public function validateOwnership(int $companyId, int $addressId): void
    {
        $address = CompanyAddress::where('id', $addressId)
            ->where('company_id', $companyId)
            ->first();

        if (!$address) {
            throw new ModelNotFoundException('The specified address does not belong to the given company.');
        }
    }
}
