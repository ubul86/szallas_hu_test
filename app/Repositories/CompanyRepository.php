<?php

namespace App\Repositories;

use App\Models\Company;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class CompanyRepository implements CompanyRepositoryInterface
{
    /**
     * @param array $filters
     * @return LengthAwarePaginator<Company>
     */
    public function index(array $filters = []): LengthAwarePaginator
    {
        $query = Company::withRelations();

        $filtersCollection = collect($filters);

        if (!empty($filters['ids'])) {
            $ids = explode(',', $filtersCollection->get('ids', []));
            $query->whereIn('id', $ids);
        }

        $perPage = $filters['itemsPerPage'] ?? 10;
        $page = $filters['page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function show(int $id): Company
    {
        try {
            return Company::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Company not found: ' . $e->getMessage());
        }
    }

    public function store(array $data): Company
    {
        try {
            return Company::create($data);
        } catch (Exception $e) {
            throw new Exception('Failed to create Company: ' . $e->getMessage());
        }
    }

    public function update(int $id, array $data): Company
    {
        try {
            $company = Company::findOrFail($id);
            $company->update($data);
            return $company;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Company not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to update Company: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): bool|null
    {
        try {
            $company = Company::findOrFail($id);
            return $company->delete();
        } catch (ModelNotFoundException $e) {
            throw new Exception('Company not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to delete Company: ' . $e->getMessage());
        }
    }

    public function checkExistsByRegistrationNumber(string $registrationNumber): bool
    {
        return Company::where('registration_number', $registrationNumber)->exists();
    }

    public function storeWithRelations(array $data): Company
    {
        DB::beginTransaction();
        try {
            $collectedData = collect($data);

            $company = Company::create($collectedData->get('company', []));

            (new Collection($collectedData->get('address', [])))->whenNotEmpty(function ($addresses) use ($company) {
                $company->address()->createMany($addresses->toArray());
            });

            (new Collection($collectedData->get('employee', [])))->whenNotEmpty(function ($employees) use ($company) {
                $company->employee()->createMany($employees->toArray());
            });

            (new Collection($collectedData->get('owner', [])))->whenNotEmpty(function ($owners) use ($company) {
                $company->owner()->createMany($owners->toArray());
            });

            DB::commit();
            return $company->load(['address', 'employee', 'owner']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function findById(int $id): Company
    {
        try {
            return Company::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw $e;
        }
    }
}
