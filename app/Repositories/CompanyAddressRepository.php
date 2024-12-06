<?php

namespace App\Repositories;

use App\Models\CompanyAddress;
use App\Repositories\Interfaces\CompanyAddressRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CompanyAddressRepository implements CompanyAddressRepositoryInterface
{
    /**
     * @param array $filters
     * @return LengthAwarePaginator<CompanyAddress>
     */
    public function index(array $filters = []): LengthAwarePaginator
    {
        $query = CompanyAddress::with('company');

        $collectedFilters = collect($filters);

        if (!$collectedFilters->has('company_id')) {
            throw new NotFoundHttpException('Company ID must be set!');
        }

        $query->where('company_id', $collectedFilters->get('company_id'));

        $perPage = $filters['itemsPerPage'] ?? 10;
        $page = $filters['page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function show(int $id): CompanyAddress
    {
        try {
            return CompanyAddress::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Company Address not found: ' . $e->getMessage());
        }
    }

    public function store(array $data, int $companyId = null): CompanyAddress
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

    public function update(int $id, array $data): CompanyAddress
    {
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

    public function destroy(int $id): bool|null
    {
        try {
            $company = CompanyAddress::findOrFail($id);
            return $company->delete();
        } catch (ModelNotFoundException $e) {
            throw new Exception('Company Address not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to delete Company Address: ' . $e->getMessage());
        }
    }
}
