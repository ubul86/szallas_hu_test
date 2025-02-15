<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Services\CompanyService;
use App\Traits\FormatsMeta;
use App\Traits\HandleJsonResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use App\Formatters\ElasticsearchFormatter;
use App\Models\Company;

/**
 * @template-use FormatsMeta<Company>
 */
class CompanyController extends Controller
{
    use HandleJsonResponse;

    /** @use FormatsMeta<Company> */
    use FormatsMeta;

    protected CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $models = $this->companyService->index($request->all());

            return $this->successResponse([
                'items' => array_map([ElasticsearchFormatter::class, 'format'], $models->getCollection()->toArray()),
                'meta' => $this->formatMeta($models),
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function store(StoreCompanyRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $company = $this->companyService->store($validated);
            return $this->successResponse(new CompanyResource($company), 201);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->successResponse(new CompanyResource($this->companyService->show($id)));
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function update(UpdateCompanyRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $company = $this->companyService->update($id, $validated);
            return $this->successResponse(new CompanyResource($company));
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->companyService->destroy($id);
            return $this->successResponse(['message' => 'Company deleted successfully']);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }
}
