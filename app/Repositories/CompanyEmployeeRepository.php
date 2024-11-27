<?php

namespace App\Repositories;

use App\Models\CompanyEmployee;
use App\Repositories\Interfaces\CompanyEmployeeRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;

class CompanyEmployeeRepository implements CompanyEmployeeRepositoryInterface
{
    /**
     * @param array $filters
     * @return LengthAwarePaginator<CompanyEmployee>
     */
    public function index(array $filters = []): LengthAwarePaginator
    {
        $query = CompanyEmployee::with('company');

        $perPage = $filters['itemsPerPage'] ?? 10;
        $page = $filters['page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function show(int $id): CompanyEmployee
    {
        try {
            return CompanyEmployee::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Company Employee not found: ' . $e->getMessage());
        }
    }

    public function store(array $data, int $companyId = null): CompanyEmployee
    {
        try {
            $employee = new CompanyEmployee();

            $employee->fill($data);
            $employee->company_id = $companyId;
            $employee->save();

            return $employee;
        } catch (Exception $e) {
            throw new Exception('Failed to create Company Employee: ' . $e->getMessage());
        }
    }

    public function update(int $id, array $data): CompanyEmployee
    {
        try {
            $companyEmployee = CompanyEmployee::findOrFail($id);
            $companyEmployee->update($data);
            return $companyEmployee;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Company Employee not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to update Company Employee: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): bool|null
    {
        try {
            $companyEmployee = CompanyEmployee::findOrFail($id);
            return $companyEmployee->delete();
        } catch (ModelNotFoundException $e) {
            throw new Exception('Company Employee not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to delete Company Employee: ' . $e->getMessage());
        }
    }
}
