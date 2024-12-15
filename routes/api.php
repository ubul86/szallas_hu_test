<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyAddressController;
use App\Http\Controllers\CompanyOwnerController;
use App\Http\Controllers\CompanyEmployeeController;

Route::get('/company/{company}/company-address', [CompanyAddressController::class, 'index']);
Route::post('company/{company}/company-address', [CompanyAddressController::class, 'store']);

Route::prefix('/company/{company}')
    ->middleware('verify.company.address')
    ->group(function () {
        Route::get('/company-address', [CompanyAddressController::class, 'index']);
        Route::get('/company-address/{id}', [CompanyAddressController::class, 'show']);
        Route::post('/company-address', [CompanyAddressController::class, 'store']);
        Route::put('/company-address/{id}', [CompanyAddressController::class, 'update']);
        Route::delete('/company-address/{id}}', [CompanyAddressController::class, 'destroy']);
});


Route::apiResource('company', CompanyController::class);
Route::apiResource('company-owner', CompanyOwnerController::class);
Route::apiResource('company-employee', CompanyEmployeeController::class);
