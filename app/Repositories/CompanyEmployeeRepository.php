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
    public function index(int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = CompanyEmployee::with('company')->where('company_id', $companyId);

        $perPage = $filters['itemsPerPage'] ?? 10;
        $page = $filters['page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function show(int $companyId, int $id): CompanyEmployee
    {
        $this->validateOwnership($companyId, $id);

        try {
            return CompanyEmployee::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Company Employee not found: ' . $e->getMessage());
        }
    }

    public function store(int $companyId, array $data): CompanyEmployee
    {
        try {
            $companyEmployee = new CompanyEmployee();

            $companyEmployee->fill($data);
            $companyEmployee->company_id = $companyId;
            $companyEmployee->save();

            return $companyEmployee;
        } catch (Exception $e) {
            throw new Exception('Failed to create Company Employee: ' . $e->getMessage());
        }
    }

    public function update(int $companyId, int $id, array $data): CompanyEmployee
    {

        $this->validateOwnership($companyId, $id);

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

    public function destroy(int $companyId, int $id): bool|null
    {

        $this->validateOwnership($companyId, $id);

        try {
            $companyEmployee = CompanyEmployee::findOrFail($id);
            return $companyEmployee->delete();
        } catch (ModelNotFoundException $e) {
            throw new Exception('Company Employee not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to delete Company Employee: ' . $e->getMessage());
        }
    }

    public function validateOwnership(int $companyId, int $employeeId): void
    {
        $employee = CompanyEmployee::where('id', $employeeId)
            ->where('company_id', $companyId)
            ->first();

        if (!$employee) {
            throw new ModelNotFoundException('The specified employee does not belong to the given company.');
        }
    }
}
