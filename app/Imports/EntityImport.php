<?php

namespace App\Imports;

use App\Models\v1\Entity;
use App\Models\v1\Field;
use App\Models\v1\Tag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EntityImport implements ToCollection, WithHeadingRow
{
    /** @var Collection */
    protected $tags;

    /** @var Collection */
    protected $fields;

    /** @var array */
    protected $attributes;

    public function __construct(Collection $tags, Collection $fields, $projectId)
    {
        $this->tags = $tags;
        $this->fields = $fields;
        $this->attributes = [
            'project_id' => $projectId,
            'created_at' => DB::raw('NOW()'),
            'updated_at' => DB::raw('NOW()'),
        ];
    }

    public static function collectionTags(Collection $rows, Collection $fields)
    {
        $tagsField = $fields->filter(fn($field) => is_string($field))->flip()->get('tags', 'Tags');
        $tagNames = $rows->flatMap(function($row) use($tagsField) {
            return Tag::parseString($row->get($tagsField));
        })->unique();
        return Tag::fromNames($tagNames);
    }

    public function createEntity($attributes, $tags, $contents)
    {
        $entity = Entity::create($attributes);
        $entity->tags()->attach($tags);
        $entity->insertContents($contents);
    }

    public function createEntityRaw($attributes, $tags, $contents)
    {
        $entity_id = DB::table('entities')->insertGetId($attributes);
        DB::table('entities_tags')->insert(collect($tags)->map(fn($tag_id) => ['tag_id' => $tag_id, 'entity_id' => $entity_id])->all());
        DB::table('values')->insert(collect($contents)->map(fn($content, $field_id) => ['entity_id' => $entity_id, 'field_id' => $field_id, 'content' => $content])->all());
    }

    public function collection(Collection $rows)
    {
        $importTags = $this->tags->pluck('id')->values();

        $tags = static::collectionTags($rows, $this->fields);
        $tagsByName = $tags->pluck('id', 'name');

        foreach ($rows as $row) {
            $attributes = $this->attributes;
            $contents = [];
            $fieldsTags = collect();
            $columnTags = collect();

            foreach ($row as $key => $content) {
                if ($field = $this->fields->get($key)) {
                    if (in_array($field, ['name', 'created_at', 'updated_at'], true)) {
                        $attributes[$field] = $content;
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

            $this->createEntityRaw($attributes, $mergedTags, $contents);
        }
    }
}
