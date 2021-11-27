<?php

namespace App\Imports;

use App\Entity;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EntityImport implements ToCollection, WithHeadingRow
{
    /** @var Collection */
    protected $tags;

    /** @var Collection */
    protected $fields;

    public function __construct(Collection $tags, Collection $fields)
    {
        $this->tags = $tags;
        $this->fields = $fields;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $entity = Entity::create([
                'name' => $row['Name'],
            ]);

            $entity->updateTags($this->tags->all());

            $contents = [];
            foreach ($row as $key => $content) {
                if ($field = $this->fields->get($key)) {
                    $contents[$field->id] = $content;
                }
            }

            $entity->updateContents($contents);
        }
    }
}
