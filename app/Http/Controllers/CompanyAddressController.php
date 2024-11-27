<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyAddressRequest;
use App\Http\Requests\UpdateCompanyAddressRequest;
use App\Repositories\CompanyAddressRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class CompanyAddressController extends Controller
{
    protected CompanyAddressRepository $companyAddressRepository;

    public function __construct(CompanyAddressRepository $companyAddressRepository)
    {
        $this->companyAddressRepository = $companyAddressRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $models = $this->companyAddressRepository->index($request->all());
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

    public function store(StoreCompanyAddressRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $companyAddress = $this->companyAddressRepository->store($validated);
            return response()->json($companyAddress, 201);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $companyAddress = $this->companyAddressRepository->show($id);
            return response()->json($companyAddress);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        }
    }

    public function update(UpdateCompanyAddressRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $companyAddress = $this->companyAddressRepository->update($id, $validated);
            return response()->json($companyAddress);
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->companyAddressRepository->destroy($id);
            return response()->json(['message' => 'Company Address deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        }
    }
}
