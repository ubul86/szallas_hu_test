<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyAddressRequest;
use App\Http\Requests\UpdateCompanyAddressRequest;
use App\Models\Company;
use App\Services\CompanyAddressService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class CompanyAddressController extends Controller
{
    protected CompanyAddressService $companyAddressService;

    public function __construct(CompanyAddressService $companyAddressService)
    {
        $this->companyAddressService = $companyAddressService;
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        $models = $this->companyAddressService->index($company->id, $request->all());
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

    public function store(Company $company, StoreCompanyAddressRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $companyAddress = $this->companyAddressService->store($company->id, $validated);
            return response()->json($companyAddress, 201);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function show(Company $company, int $id): JsonResponse
    {
        try {
            $companyAddress = $this->companyAddressService->show($company->id, $id);
            return response()->json($companyAddress);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        }
    }

    public function update(Company $company, UpdateCompanyAddressRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $companyAddress = $this->companyAddressService->update($company->id, $id, $validated);
            return response()->json($companyAddress);
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function destroy(Company $company, int $id): JsonResponse
    {
        try {
            $this->companyAddressService->destroy($company->id, $id);
            return response()->json(['message' => 'Company Address deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        }
    }
}
