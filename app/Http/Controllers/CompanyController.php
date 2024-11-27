<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CompanyController extends Controller
{
    protected CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $models = $this->companyService->index($request->all());
            return response()->json([
                'items' => CompanyResource::collection($models->items()),
                'meta' => [
                    'current_page' => $models->currentPage(),
                    'total_pages' => $models->lastPage(),
                    'total_items' => $models->total(),
                    'items_per_page' => $models->perPage(),
                ],
            ]);
        } catch (NotFoundHttpException $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function store(StoreCompanyRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $company = $this->companyService->store($validated);
            return response()->json(new CompanyResource($company), 201);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $company = $this->companyService->show($id);
            return response()->json(new CompanyResource($company));
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        }
    }

    public function update(UpdateCompanyRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $company = $this->companyService->update($id, $validated);
            return response()->json(new CompanyResource($company));
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->companyService->destroy($id);
            return response()->json(['message' => 'Company deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        }
    }
}
