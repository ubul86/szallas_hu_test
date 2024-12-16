<?php

namespace Tests\Unit\app\Repositories;

use Tests\TestCase;
use App\Models\Company;
use App\Models\CompanyAddress;
use App\Repositories\CompanyAddressRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CompanyAddressRepositoryTest extends TestCase
{
    use RefreshDatabase;
    protected CompanyAddressRepository $companyAddressRepository;

    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->companyAddressRepository = new CompanyAddressRepository();
        $this->company = Company::factory()->create();
    }

    /** @test */
    public function index_with_filters()
    {
        CompanyAddress::factory(15)->create(['company_id' => $this->company->id]);
        $filters = ['itemsPerPage' => 5, 'page' => 1];

        $result = $this->companyAddressRepository->index($this->company->id, $filters);

        $this->assertCount(5, $result->items());
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    /** @test */
    public function show_with_valid_id()
    {
        $companyAddress = CompanyAddress::factory()->create(['company_id' => $this->company->id]);

        $repositoryMock = Mockery::mock(CompanyAddressRepository::class)->makePartial();

        $repositoryMock
            ->shouldReceive('validateOwnership')
            ->once()
            ->with($this->company->id, $companyAddress->id);

        $this->app->instance(CompanyAddressRepository::class, $repositoryMock);

        $address = $repositoryMock->show($this->company->id, $companyAddress->id);

        $this->assertInstanceOf(CompanyAddress::class, $address);
        $this->assertEquals($companyAddress->id, $address->id);
    }

    /** @test */
    public function show_with_invalid_id()
    {
        $this->expectException(ModelNotFoundException::class);

        $this->companyAddressRepository->show($this->company->id, 999);
    }

    /** @test */
    public function store()
    {
        $data = [
            'country' => 'Test Country',
            'street_address' => 'Apt 4B',
            'city' => 'Test City',
            'latitude' => '1',
            'longitude' => '1',
        ];

        $address = $this->companyAddressRepository->store($this->company->id, $data);

        $this->assertInstanceOf(CompanyAddress::class, $address);
        $this->assertEquals($data['country'], $address->country);
        $this->assertEquals($this->company->id, $address->company_id);
    }

    /** @test */
    public function update_with_valid_id()
    {

        $companyAddress = CompanyAddress::factory()->create(['company_id' => $this->company->id]);

        $repositoryMock = Mockery::mock(CompanyAddressRepository::class)->makePartial();

        $repositoryMock
            ->shouldReceive('validateOwnership')
            ->once()
            ->with($this->company->id, $companyAddress->id);

        $this->app->instance(CompanyAddressRepository::class, $repositoryMock);

        $newData = [
            'street_address' => '456 Avenue',
            'city' => 'Updated City',
        ];

        $result = $repositoryMock->update($this->company->id, $companyAddress->id, $newData);

        $this->assertInstanceOf(CompanyAddress::class, $result);
        $this->assertEquals($newData['street_address'], $result->street_address);
        $this->assertEquals($newData['city'], $result->city);
    }

    /** @test */
    public function update_with_invalid_id()
    {
        $this->expectException(ModelNotFoundException::class);

        $newData = ['street_address' => '456 Avenue'];

        $this->companyAddressRepository->update($this->company->id, 999, $newData);
    }

    /** @test */
    public function destroy_with_valid_id()
    {
        $companyAddress = CompanyAddress::factory()->create(['company_id' => $this->company->id]);

        $repositoryMock = Mockery::mock(CompanyAddressRepository::class)->makePartial();

        $repositoryMock
            ->shouldReceive('validateOwnership')
            ->once()
            ->with($this->company->id, $companyAddress->id);

        $this->app->instance(CompanyAddressRepository::class, $repositoryMock);

        $result = $repositoryMock->destroy($this->company->id, $companyAddress->id);

        $this->assertTrue($result);
    }

    /** @test */
    public function destroy_with_invalid_id()
    {
        $this->expectException(ModelNotFoundException::class);

        $this->companyAddressRepository->destroy($this->company->id, 999);
    }

    /** @test */
    public function validateOwnership_with_valid_data()
    {

        $companyAddress = CompanyAddress::factory()->create(['company_id' => $this->company->id]);

        $this->companyAddressRepository->validateOwnership($this->company->id, $companyAddress->id);

        $this->assertTrue(true);
    }

    /** @test */
    public function validateOwnership_with_invalid_data()
    {
        $this->expectException(ModelNotFoundException::class);

        $this->companyAddressRepository->validateOwnership($this->company->id, 999);
    }
}
