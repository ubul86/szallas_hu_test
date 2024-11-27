<?php

namespace App\Repositories;

use App\Models\CompanyOwner;
use App\Repositories\Interfaces\CompanyOwnerRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;

class CompanyOwnerRepository implements CompanyOwnerRepositoryInterface
{
    /**
     * @param array $filters
     * @return LengthAwarePaginator<CompanyOwner>
     */
    public function index(array $filters = []): LengthAwarePaginator
    {
        $query = CompanyOwner::with('company');

        $perPage = $filters['itemsPerPage'] ?? 10;
        $page = $filters['page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function show(int $id): CompanyOwner
    {
        try {
            return CompanyOwner::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Company Owner not found: ' . $e->getMessage());
        }
    }

    public function store(array $data, int $companyId = null): CompanyOwner
    {
        try {
            $companyOwner = new CompanyOwner();

            $companyOwner->fill($data);
            $companyOwner->company_id = $companyId;
            $companyOwner->save();

            return $companyOwner;
        } catch (Exception $e) {
            throw new Exception('Failed to create Company Owner: ' . $e->getMessage());
        }
    }

    public function update(int $id, array $data): CompanyOwner
    {
        try {
            $companyOwner = CompanyOwner::findOrFail($id);
            $companyOwner->update($data);
            return $companyOwner;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Company Address not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to update Company Owner: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): bool|null
    {
        try {
            $companyOwner = CompanyOwner::findOrFail($id);
            return $companyOwner->delete();
        } catch (ModelNotFoundException $e) {
            throw new Exception('Company Address not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to delete Company Owner: ' . $e->getMessage());
        }
    }
}
