<?php

namespace App\Imports;

use App\Entity;
use App\Field;
use App\Tag;
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

    public static function collectionTags(Collection $rows, Collection $fields)
    {
        $tagsField = $fields->filter(fn($field) => is_string($field))->flip()->get('tags', 'Tags');
        return $rows->flatMap(function($row) use($tagsField) {
            return Tag::parseString($row->get($tagsField));
        })->unique();
    }

    public function collection(Collection $rows)
    {
        $importTags = $this->tags->pluck('id')->values();

        $tags = static::collectionTags($rows, $this->fields)->map(function($name) {
            return Tag::firstOrCreate(['name' => $name]);
        });
        $tagsByName = $tags->pluck('id', 'name');

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
                        $columnTags = collect(Tag::parseString($content))->map(function($name) use($tagsByName) {
                            return $tagsByName[$name];
                        });
                    } elseif ($content && $field instanceof Field) {
                        $fieldsTags->push($field->tag_id);
                        $contents[$field->id] = $content;
                    }
                }
            }

            $mergedTags = $importTags->concat($fieldsTags)->concat($columnTags)->unique()->all();

            $entity->save();
            $entity->tags()->attach($mergedTags);
            $entity->insertContents($contents);
        }
    }
}
