<?php

namespace App\Repositories;

use App\Models\Company;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class CompanyRepository implements CompanyRepositoryInterface
{
    /**
     * @var Builder<Company>
     */
    protected Builder $model;

    public function __construct()
    {
        $this->model = Company::query();
    }

    /**
     * @return Builder<Company>
     */
    protected function query(): Builder
    {
        return Company::withRelations();
    }

    public function index(array $filters = []): LengthAwarePaginator
    {
        $query = $this->query();
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
        return $this->query()->findOrFail($id);
    }

    public function store(array $data): Company
    {
        try {
            return $this->query()->create($data);
        } catch (Exception $e) {
            throw new Exception('Failed to create Company: ' . $e->getMessage());
        }
    }

    public function update(int $id, array $data): Company
    {
        try {
            $company = $this->query()->findOrFail($id);
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
            return $this->query()->findOrFail($id)->delete();
        } catch (ModelNotFoundException $e) {
            throw new Exception('Company not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to delete Company: ' . $e->getMessage());
        }
    }

    public function checkExistsByRegistrationNumber(string $registrationNumber): bool
    {
        return $this->query()->where('registration_number', $registrationNumber)->exists();
    }

    public function storeWithRelations(array $data): Company
    {
        DB::beginTransaction();
        try {
            $collectedData = collect($data);

            $company = $this->query()->create($collectedData->get('company', []));

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
        return $this->query()->findOrFail($id);
    }

    public function getCompanyIds(): array
    {
        return $this->query()->pluck('updated_at', 'id')->toArray();
    }
}
