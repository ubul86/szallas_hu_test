<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyEmployeeRequest;
use App\Http\Requests\UpdateCompanyEmployeeRequest;
use App\Models\Company;
use App\Models\CompanyEmployee;
use App\Services\CompanyEmployeeService;
use App\Traits\FormatsMeta;
use App\Traits\HandleJsonResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class CompanyEmployeeController extends Controller
{
    use HandleJsonResponse;

    /** @use FormatsMeta<CompanyEmployee> */
    use FormatsMeta;

    protected CompanyEmployeeService $companyEmployeeService;

    public function __construct(CompanyEmployeeService $companyEmployeeService)
    {
        $this->companyEmployeeService = $companyEmployeeService;
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        try {
            $models = $this->companyEmployeeService->index($company->id, $request->all());

            return $this->successResponse([
                'items' => $models->items(),
                'meta' => $this->formatMeta($models),
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function store(Company $company, StoreCompanyEmployeeRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $companyEmployee = $this->companyEmployeeService->store($company->id, $validated);
            return $this->successResponse($companyEmployee, 201);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function show(Company $company, int $id): JsonResponse
    {
        try {
            $companyEmployee = $this->companyEmployeeService->show($company->id, $id);
            return $this->successResponse($companyEmployee);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function update(Company $company, UpdateCompanyEmployeeRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $companyEmployee = $this->companyEmployeeService->update($company->id, $id, $validated);
            return $this->successResponse($companyEmployee);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function destroy(Company $company, int $id): JsonResponse
    {
        try {
            $this->companyEmployeeService->destroy($company->id, $id);
            return $this->successResponse(['message' => 'Company Employee deleted successfully']);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }
}
