<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyOwnerRequest;
use App\Http\Requests\UpdateCompanyOwnerRequest;
use App\Repositories\CompanyOwnerRepository;
use App\Repositories\Interfaces\CompanyOwnerRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class CompanyOwnerController extends Controller
{
    protected CompanyOwnerRepositoryInterface $companyOwnerRepository;

    public function __construct(CompanyOwnerRepositoryInterface $companyOwnerRepository)
    {
        $this->companyOwnerRepository = $companyOwnerRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $models = $this->companyOwnerRepository->index($request->all());
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

    public function store(StoreCompanyOwnerRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $companyOwner = $this->companyOwnerRepository->store($validated);
            return response()->json($companyOwner, 201);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $companyOwner = $this->companyOwnerRepository->show($id);
            return response()->json($companyOwner);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        }
    }

    public function update(UpdateCompanyOwnerRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $companyOwner = $this->companyOwnerRepository->update($id, $validated);
            return response()->json($companyOwner);
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->companyOwnerRepository->destroy($id);
            return response()->json(['message' => 'Company Owner deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        }
    }
}
