<?php

namespace App\Console\Commands;

use App\Imports\PersonsImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Webkul\Contact\Repositories\PersonRepository;

class ImportPersons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:persons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import persons';

    protected $organizationRepository;

    public function __construct(PersonRepository $personRepository)
    {
        parent::__construct();

        $this->personRepository = $personRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Excel::import(new PersonsImport($this->personRepository), 'LinkedIn Tracking Sheet.xlsm');

        $this->info("Persons imported successfully!");
    }
}
