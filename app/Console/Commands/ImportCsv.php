<?php

namespace App\Console\Commands;

use App\Models\CompanyEmployee;
use App\Repositories\UserRepository;
use App\Services\CompanyService;
use Illuminate\Console\Command;
use League\Csv\Reader;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;

class ImportCsv extends Command
{
    protected $signature = 'import:csv';

    protected $description = 'Import a CSV file into the database';

    private const DATA_FILE = 'database/seeders/datas/data.csv';

    protected CompanyService $companyService;
    protected UserRepository $userRepository;

    public function __construct(CompanyService $companyService, UserRepository $userRepository)
    {
        parent::__construct();

        $this->companyService = $companyService;
        $this->userRepository = $userRepository;
    }

    public function handle(): void
    {
        $csvPath = base_path(self::DATA_FILE);

        if (!file_exists($csvPath)) {
            $this->error("The file does not exist.");
            return;
        }

        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(';');

        foreach ($csv->getRecords() as $row) {
            try {
                $record = collect($row)->map(fn($value) => trim($value));

                if ($this->companyExists($record->get('companyRegistrationNumber', ''))) {
                    $this->warn("Company already exists: {$record->get('companyRegistrationNumber')}");
                    continue;
                }

                $this->importCompanyData($record);
                $this->importUserData($record);
            } catch (\Exception $e) {
                var_dump($row);
            }
        }

        $this->info('CSV import completed successfully!');
    }

    private function companyExists(string $registrationNumber): bool
    {
        return $this->companyService->checkExistsByRegistrationNumber($registrationNumber);
    }

    /** @param Collection<string, string> $record */
    private function importCompanyData(Collection $record): void
    {
        $dataToSave = [
            'company' => [
                'name' => $record->get('companyName'),
                'registration_number' => $record->get('companyRegistrationNumber'),
                'foundation_date' => $record->get('companyFoundationDate', null),
                'activity' => $record->get('activity', null),
                'active' => (int) $record->get('active', 0),
            ],
            'address' => [[
                'country' => $record->get('country'),
                'zip_code' => $record->get('zipCode'),
                'city' => $record->get('city', null),
                'street_address' => $record->get('streetAddress', null),
                'latitude' => $record->get('latitude', false),
                'longitude' => $record->get('longitude', false),
            ]],
            'owner' => [[
                'name' => $record->get('companyOwner'),
            ]],
        ];

        $company = $this->companyService->storeWithRelations($dataToSave);

        $employeesCount = (int) $record->get('employees', 0);

        CompanyEmployee::factory($employeesCount)->create(['company_id' => $company->id]);
    }

    /** @param Collection<string, string> $record */
    private function importUserData(Collection $record): void
    {
        $userData = [
            'email' => strtolower($record->get('email', '')),
            'password' => Hash::make($record->get('password', '')),
        ];

        $this->userRepository->store($userData);
    }
}
