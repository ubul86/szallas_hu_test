<?php

namespace App\Providers;

use App\Repositories\CompanyAddressRepository;
use App\Repositories\CompanyElasticsearchRepository;
use App\Repositories\CompanyEmployeeRepository;
use App\Repositories\CompanyOwnerRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\Interfaces\CompanyAddressRepositoryInterface;
use App\Repositories\Interfaces\CompanyElasticsearchRepositoryInterface;
use App\Repositories\Interfaces\CompanyEmployeeRepositoryInterface;
use App\Repositories\Interfaces\CompanyOwnerRepositoryInterface;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(CompanyAddressRepositoryInterface::class, CompanyAddressRepository::class);
        $this->app->bind(CompanyOwnerRepositoryInterface::class, CompanyOwnerRepository::class);
        $this->app->bind(CompanyEmployeeRepositoryInterface::class, CompanyEmployeeRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CompanyElasticsearchRepositoryInterface::class, CompanyElasticsearchRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
