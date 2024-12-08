<?php

namespace App\Services;

use App\Repositories\Interfaces\CompanyElasticsearchRepositoryInterface;
use App\Repositories\Interfaces\CompanyRepositoryInterface;

class SyncDatasService
{
    protected CompanyRepositoryInterface $companyRepository;
    protected CompanyElasticsearchRepositoryInterface $companyElasticsearchRepository;

    public function __construct(CompanyRepositoryInterface $companyRepository, CompanyElasticsearchRepositoryInterface $companyElasticsearchRepository)
    {
        $this->companyRepository = $companyRepository;
        $this->companyElasticsearchRepository = $companyElasticsearchRepository;
    }

    public function getElasticRecords(): array
    {
        return $this->companyElasticsearchRepository->getElasticRecords();
    }

    public function getMysqlRecords(): array
    {
        return $this->companyRepository->getCompanyIds();
    }

    public function getRecordsToDelete(array $elasticIds, array $mysqlIds): array
    {
        return array_diff($elasticIds, $mysqlIds);
    }

    public function getRecordsToIndex(array $elasticIds, array $mysqlIds): array
    {
        return array_diff($mysqlIds, $elasticIds);
    }

    public function getRecordsToSync(array $mysqlCompanies, array $elasticRecords): array
    {
        $toDataSync = [];
        foreach ($mysqlCompanies as $id => $mysqlUpdatedAt) {
            if (isset($elasticRecords[$id]) && $elasticRecords[$id] !== $mysqlUpdatedAt) {
                $toDataSync[] = $id;
            }
        }

        return $toDataSync;
    }
}
