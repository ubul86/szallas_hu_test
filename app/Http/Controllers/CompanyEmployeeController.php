<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyEmployeeRequest;
use App\Http\Requests\UpdateCompanyEmployeeRequest;
use App\Repositories\CompanyEmployeeRepository;
use App\Repositories\Interfaces\CompanyEmployeeRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class CompanyEmployeeController extends Controller
{
    protected CompanyEmployeeRepositoryInterface $companyEmployeeRepository;

    public function __construct(CompanyEmployeeRepositoryInterface $companyEmployeeRepository)
    {
        $this->companyEmployeeRepository = $companyEmployeeRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $models = $this->companyEmployeeRepository->index($request->all());
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


    public function store(StoreCompanyEmployeeRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $companyEmployee = $this->companyEmployeeRepository->store($validated);
            return response()->json($companyEmployee, 201);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $companyEmployee = $this->companyEmployeeRepository->show($id);
            return response()->json($companyEmployee);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        }
    }

    public function update(UpdateCompanyEmployeeRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $companyEmployee = $this->companyEmployeeRepository->update($id, $validated);
            return response()->json($companyEmployee);
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->companyEmployeeRepository->destroy($id);
            return response()->json(['message' => 'Company Employee deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 404);
        }
    }
}
