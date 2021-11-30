<?php

namespace App\Imports;

use App\Entity;
use App\Field;
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

    protected function getTags($tagsString)
    {
        $tagNames = preg_split('/\s*,\s*/', trim($tagsString), -1, PREG_SPLIT_NO_EMPTY);
        return array_map(function($name) {
            return [
                'name' => $name,
            ];
        }, $tagNames);
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $entity = new Entity;

            $contents = [];
            $fieldsTags = collect();
            $columnTags = collect();

            foreach ($row as $key => $content) {
                if ($field = $this->fields->get($key)) {
                    if (in_array($field, ['name', 'created_at', 'updated_at'])) {
                        $entity->{$field} = $content;
                    } elseif ($field === 'tags') {
                        $columnTags = $this->getTags($content);
                    } elseif ($content && $field instanceof Field) {
                        $fieldsTags->push($field->tag);
                        $contents[$field->id] = $content;
                    }
                }
            }

            $mergedTags = $this->tags->concat($fieldsTags)->concat($columnTags)->unique('name')->all();

            $entity->save();
            $entity->updateTags($mergedTags);
            $entity->insertContents($contents);
        }
    }
}
