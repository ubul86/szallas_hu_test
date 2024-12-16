<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyOwnerRequest;
use App\Http\Requests\UpdateCompanyOwnerRequest;
use App\Models\Company;
use App\Services\CompanyOwnerService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class CompanyOwnerController extends Controller
{
    protected CompanyOwnerService $companyOwnerService;

    public function __construct(CompanyOwnerService $companyOwnerService)
    {
        $this->companyOwnerService = $companyOwnerService;
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        $models = $this->companyOwnerService->index($company->id, $request->all());
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

    public function store(Company $company, StoreCompanyOwnerRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $companyOwner = $this->companyOwnerService->store($company->id, $validated);
            return response()->json($companyOwner, 201);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function show(Company $company, int $id): JsonResponse
    {
        try {
            $companyOwner = $this->companyOwnerService->show($company->id, $id);
            return response()->json($companyOwner);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        }
    }

    public function update(Company $company, UpdateCompanyOwnerRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $companyOwner = $this->companyOwnerService->update($company->id, $id, $validated);
            return response()->json($companyOwner);
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function destroy(Company $company, int $id): JsonResponse
    {
        try {
            $this->companyOwnerService->destroy($company->id, $id);
            return response()->json(['message' => 'Company Owner deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        }
    }
}
