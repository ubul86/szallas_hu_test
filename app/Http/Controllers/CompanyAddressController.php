<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyAddressRequest;
use App\Http\Requests\UpdateCompanyAddressRequest;
use App\Models\Company;
use App\Models\CompanyAddress;
use App\Services\CompanyAddressService;
use App\Traits\FormatsMeta;
use App\Traits\HandleJsonResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class CompanyAddressController extends Controller
{
    use HandleJsonResponse;

    /** @use FormatsMeta<CompanyAddress> */
    use FormatsMeta;

    protected CompanyAddressService $companyAddressService;

    public function __construct(CompanyAddressService $companyAddressService)
    {
        $this->companyAddressService = $companyAddressService;
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        try {
            $models = $this->companyAddressService->index($company->id, $request->all());

            return $this->successResponse([
                'items' => $models->items(),
                'meta' => $this->formatMeta($models),
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function store(Company $company, StoreCompanyAddressRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $companyAddress = $this->companyAddressService->store($company->id, $validated);
            return $this->successResponse($companyAddress, 201);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function show(Company $company, int $id): JsonResponse
    {
        try {
            $companyAddress = $this->companyAddressService->show($company->id, $id);
            return $this->successResponse($companyAddress);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function update(Company $company, UpdateCompanyAddressRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $companyAddress = $this->companyAddressService->update($company->id, $id, $validated);
            return $this->successResponse($companyAddress);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function destroy(Company $company, int $id): JsonResponse
    {
        try {
            $this->companyAddressService->destroy($company->id, $id);
            return $this->successResponse(['message' => 'Company Address deleted successfully']);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }
}
