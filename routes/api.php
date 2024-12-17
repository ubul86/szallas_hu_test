<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyAddressController;
use App\Http\Controllers\CompanyOwnerController;
use App\Http\Controllers\CompanyEmployeeController;

Route::prefix('/company/{company}')
    ->middleware('verify.company.address')
    ->group(function () {
        Route::get('/company-address', [CompanyAddressController::class, 'index']);
        Route::get('/company-address/{id}', [CompanyAddressController::class, 'show']);
        Route::post('/company-address', [CompanyAddressController::class, 'store']);
        Route::put('/company-address/{id}', [CompanyAddressController::class, 'update']);
        Route::delete('/company-address/{id}', [CompanyAddressController::class, 'destroy']);
});

Route::prefix('/company/{company}')
    ->middleware('verify.company.owner')
    ->group(function () {
        Route::get('/company-owner', [CompanyOwnerController::class, 'index']);
        Route::get('/company-owner/{id}', [CompanyOwnerController::class, 'show']);
        Route::post('/company-owner', [CompanyOwnerController::class, 'store']);
        Route::put('/company-owner/{id}', [CompanyOwnerController::class, 'update']);
        Route::delete('/company-owner/{id}', [CompanyOwnerController::class, 'destroy']);
    });

Route::prefix('/company/{company}')
    ->middleware('verify.company.employee')
    ->group(function () {
        Route::get('/company-employee', [CompanyEmployeeController::class, 'index']);
        Route::get('/company-employee/{id}', [CompanyEmployeeController::class, 'show']);
        Route::post('/company-employee', [CompanyEmployeeController::class, 'store']);
        Route::put('/company-employee/{id}', [CompanyEmployeeController::class, 'update']);
        Route::delete('/company-employee/{id}', [CompanyEmployeeController::class, 'destroy']);
    });

Route::apiResource('company', CompanyController::class);
