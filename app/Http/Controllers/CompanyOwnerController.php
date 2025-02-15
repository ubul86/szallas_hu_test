<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyOwnerRequest;
use App\Http\Requests\UpdateCompanyOwnerRequest;
use App\Models\Company;
use App\Models\CompanyOwner;
use App\Services\CompanyOwnerService;
use App\Traits\FormatsMeta;
use App\Traits\HandleJsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class CompanyOwnerController extends Controller
{
    use HandleJsonResponse;

    /** @use FormatsMeta<CompanyOwner> */
    use FormatsMeta;

    protected CompanyOwnerService $companyOwnerService;

    public function __construct(CompanyOwnerService $companyOwnerService)
    {
        $this->companyOwnerService = $companyOwnerService;
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        try {
            $models = $this->companyOwnerService->index($company->id, $request->all());

            return $this->successResponse([
                'items' => $models->items(),
                'meta' => $this->formatMeta($models),
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function store(Company $company, StoreCompanyOwnerRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $companyOwner = $this->companyOwnerService->store($company->id, $validated);
            return $this->successResponse($companyOwner, 201);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function show(Company $company, int $id): JsonResponse
    {
        try {
            $companyOwner = $this->companyOwnerService->show($company->id, $id);
            return $this->successResponse($companyOwner);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function update(Company $company, UpdateCompanyOwnerRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $companyOwner = $this->companyOwnerService->update($company->id, $id, $validated);
            return $this->successResponse($companyOwner);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function destroy(Company $company, int $id): JsonResponse
    {
        try {
            $this->companyOwnerService->destroy($company->id, $id);
            return $this->successResponse(['message' => 'Company Owner deleted successfully']);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }
}
