<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyAddressController;
use App\Http\Controllers\CompanyOwnerController;
use App\Http\Controllers\CompanyEmployeeController;

Route::get('/company/{company}/company-address', [CompanyAddressController::class, 'index']);


Route::apiResource('company', CompanyController::class);
Route::apiResource('company-owner', CompanyOwnerController::class);
Route::apiResource('company-employee', CompanyEmployeeController::class);
