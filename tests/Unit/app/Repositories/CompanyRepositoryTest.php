<?php

namespace Tests\Unit\app\Repositories;

use Tests\TestCase;
use App\Models\Company;
use App\Repositories\CompanyRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class CompanyRepositoryTest extends TestCase
{
    /** @var CompanyRepository  */
    protected CompanyRepository $companyRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->companyRepository = new CompanyRepository();
    }

    /** @test */
    public function it_returns_paginated_companies(): void
    {
        Company::factory(15)->create();
        $filters = ['page' => 1, 'itemsPerPage' => 10];

        $result = $this->companyRepository->index($filters);

        $this->assertCount(10, $result->items());
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    /** @test */
    public function it_finds_a_company_by_id(): void
    {
        $company = Company::factory()->create();

        $result = $this->companyRepository->show($company->id);

        $this->assertEquals($company->id, $result->id);
    }

    /** @test */
    public function it_creates_a_company(): void
    {
        $data = [
            'name' => 'Test Company',
            'registration_number' => '12345678',
            'foundation_date' => '2024-01-01',
            'activity' => 'IT Services',
            'active' => true,
        ];

        $company = $this->companyRepository->store($data);

        $this->assertDatabaseHas('companies', ['name' => 'Test Company']);
        $this->assertEquals('Test Company', $company->name);
    }

    /** @test */
    public function it_updates_a_company(): void
    {
        $company = Company::factory()->create(['name' => 'Old Company Name']);

        $updatedCompany = $this->companyRepository->update($company->id, ['name' => 'New Company Name']);

        $this->assertEquals('New Company Name', $updatedCompany->name);
        $this->assertDatabaseHas('companies', ['name' => 'New Company Name']);
    }

    /** @test */
    public function it_throws_exception_when_updating_foundation_date(): void
    {
        $company = Company::factory()->create(['foundation_date' => '2024-11-26']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The foundation_date field cannot be modified');

        $this->companyRepository->update($company->id, ['foundation_date' => '2025-01-01']);
    }

    /** @test */
    public function it_deletes_a_company(): void
    {
        $company = Company::factory()->create();

        $result = $this->companyRepository->destroy($company->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }

    /** @test */
    public function it_throws_exception_when_deleting_nonexistent_company(): void
    {
        $this->expectException(\Exception::class);
        $this->companyRepository->destroy(999);
    }

    /** @test */
    public function it_checks_existence_by_registration_number(): void
    {
        Company::factory()->create(['registration_number' => '12345678']);

        $exists = $this->companyRepository->checkExistsByRegistrationNumber('12345678');
        $this->assertTrue($exists);

        $notExists = $this->companyRepository->checkExistsByRegistrationNumber('87654321');
        $this->assertFalse($notExists);
    }
}
