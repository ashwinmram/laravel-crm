<?php

namespace App\Console\Commands;

use App\Imports\OrganizationsImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Webkul\Contact\Repositories\OrganizationRepository;

class ImportOrganizations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:organizations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import organizations';

    protected $organizationRepository;

    public function __construct(OrganizationRepository $organizationRepository)
    {
        parent::__construct();

        $this->organizationRepository = $organizationRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Excel::import(new OrganizationsImport($this->organizationRepository), 'LinkedIn Tracking Sheet.xlsm');

        $this->info("Organizations imported successfully!");
    }
}
