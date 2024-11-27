<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyAddressController;
use App\Http\Controllers\CompanyOwnerController;
use App\Http\Controllers\CompanyEmployeeController;

Route::apiResource('company', CompanyController::class);
Route::apiResource('company-owner', CompanyOwnerController::class);
Route::apiResource('company-employee', CompanyEmployeeController::class);
Route::apiResource('company-address', CompanyAddressController::class);
