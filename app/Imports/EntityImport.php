<?php

namespace App\Imports;

use App\Entity;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EntityImport implements ToCollection, WithHeadingRow
{
    protected $tags;

    public function __construct($tags)
    {
        $this->tags = $tags;
    }

    public function collection(Collection $rows)
    {
        /** @var Collection */
        $fields = $this->tags->pluck('fields')->flatten()->keyBy('name');

        foreach ($rows as $row) {
            $entity = Entity::create([
                'name' => $row['name'],
            ]);

            $entity->updateTags($this->tags->all());

            $contents = [];
            foreach ($row as $key => $content) {
                if ($field = $fields->get($key)) {
                    $contents[$field->id] = $content;
                }
            }

            $entity->updateContents($contents);
        }
    }
}
