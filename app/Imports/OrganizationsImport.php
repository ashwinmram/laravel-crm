<?php

namespace App\Imports;

use Illuminate\Support\Facades\Event;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Webkul\Contact\Models\Organization;
use Webkul\Contact\Repositories\OrganizationRepository;

class OrganizationsImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithMultipleSheets
{
    use Importable;

    /**
     * OrganizationRepository object
     *
     * @var \Webkul\Product\Repositories\OrganizationRepository
     */
    protected $organizationRepository;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\Product\Repositories\OrganizationRepository  $organizationRepository
     *
     * @return void
     */
    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (!Organization::where('name', $row['company'])->count()) {
            Event::dispatch('contacts.organization.create.before');

            $organization = $this->organizationRepository->create([
                'name' => $row['company'],
                'entity_type' => 'organizations',
            ]);

            Event::dispatch('contacts.organization.create.after', $organization);

            return $organization;
        }
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }
}
