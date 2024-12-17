<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyEmployeeRequest;
use App\Http\Requests\UpdateCompanyEmployeeRequest;
use App\Models\Company;
use App\Services\CompanyEmployeeService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class CompanyEmployeeController extends Controller
{
    protected CompanyEmployeeService $companyEmployeeService;

    public function __construct(CompanyEmployeeService $companyEmployeeService)
    {
        $this->companyEmployeeService = $companyEmployeeService;
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        $models = $this->companyEmployeeService->index($company->id, $request->all());
        return response()->json([
            'items' => $models->items(),
            'meta' => [
                'current_page' => $models->currentPage(),
                'total_pages' => $models->lastPage(),
                'total_items' => $models->total(),
                'items_per_page' => $models->perPage(),
            ],
        ]);
    }

    public function store(Company $company, StoreCompanyEmployeeRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $companyEmployee = $this->companyEmployeeService->store($company->id, $validated);
            return response()->json($companyEmployee, 201);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function show(Company $company, int $id): JsonResponse
    {
        try {
            $companyEmployee = $this->companyEmployeeService->show($company->id, $id);
            return response()->json($companyEmployee);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        }
    }

    public function update(Company $company, UpdateCompanyEmployeeRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $companyEmployee = $this->companyEmployeeService->update($company->id, $id, $validated);
            return response()->json($companyEmployee);
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function destroy(Company $company, int $id): JsonResponse
    {
        try {
            $this->companyEmployeeService->destroy($company->id, $id);
            return response()->json(['message' => 'Company Employee deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        }
    }
}
