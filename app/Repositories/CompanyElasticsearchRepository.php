<?php

namespace App\Repositories;

use App\Models\Company;
use App\Repositories\Interfaces\CompanyElasticsearchRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;

class CompanyElasticsearchRepository implements CompanyElasticsearchRepositoryInterface
{

    public function search(array $filters): LengthAwarePaginator
    {
        // TODO: Implement search() method.
    }

    public function index(array $filters = []): LengthAwarePaginator
    {
        // TODO: Implement index() method.
    }

    public function show(int $id): Company
    {
        // TODO: Implement show() method.
    }

    public function store(array $data): Company
    {
        // TODO: Implement store() method.
    }

    public function update(int $id, array $data): Company
    {
        // TODO: Implement update() method.
    }

    public function destroy(int $id): bool|null
    {
        // TODO: Implement destroy() method.
    }

    public function checkExistsByRegistrationNumber(string $registrationNumber): bool
    {
        // TODO: Implement checkExistsByRegistrationNumber() method.
    }
}
