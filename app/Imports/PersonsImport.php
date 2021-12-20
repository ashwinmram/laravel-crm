<?php

namespace App\Imports;

use Illuminate\Support\Facades\Event;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Webkul\Contact\Models\Organization;
use Webkul\Contact\Models\Person;
use Webkul\Contact\Repositories\PersonRepository;

class PersonsImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithMultipleSheets
{
    use Importable;

    public function __construct(PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (!Person::where('name', $row['first_name'] . " " . $row['last_name'])->where('emails', 'like', "%{$row['email_address']}%")->count()) {
            Event::dispatch('contacts.person.create.before');

            $person = $this->personRepository->create([
                'name' => $row['first_name'] . " " . $row['last_name'],
                'emails' => [[
                    'value' => $row['email_address'],
                    'label' => 'home',
                ]],
                'contact_numbers' => [[
                    'value' => $row['mobile'],
                    'label' => 'home',
                ]],
                'linkedin_url' => $row['linkedin_url'],
                'organization_id' => Organization::where('name', $row['company'])->count() ? Organization::where('name', $row['company'])->first()->id : null,
                'entity_type' => 'persons',
            ]);

            Event::dispatch('contacts.person.create.after', $person);

            return $person;
        } else {
            $id = Person::where('name', $row['first_name'] . " " . $row['last_name'])->where('emails', 'like', "%\"{$row['email_address']}\"%")->first()->id;

            Event::dispatch('contacts.person.update.before', $id);

            $person = $this->personRepository->update([
                'name' => $row['first_name'] . " " . $row['last_name'],
                'emails' => [[
                    'value' => $row['email_address'],
                    'label' => 'home',
                ]],
                'contact_numbers' => [[
                    'value' => $row['mobile'],
                    'label' => 'home',
                ]],
                'linkedin_url' => $row['linkedin_url'],
                'organization_id' => Organization::where('name', $row['company'])->count() ? Organization::where('name', $row['company'])->first()->id : null,
                'entity_type' => 'persons',
            ], $id);

            Event::dispatch('contacts.person.update.after', $person);
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
