<?php

namespace App\Http\Controllers\Api\v1;

use App\Tag;
use App\Field;
use App\Preset;
use App\Imports\EntityImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function store(Request $request)
    {
        $importFile = $request->file('importFile');
        $tags = collect();
        $fields = collect($request->get('fields'))->map(function($field_id) {
            return is_numeric($field_id) ? Field::find($field_id) : $field_id;
        });
        $tagsField = $fields->filter(fn($field) => is_string($field))->flip()->get('tags', 'Tags');

        if ($request->has('preview')) {
            $preview = $request->get('preview');
            $headings = (new HeadingRowImport)->toArray($importFile);
            $rows = Excel::toCollection(new EntityImport($tags, $fields), $importFile);
            $tags = $rows->first()->flatMap(function($row) use($tagsField) {
                return preg_split('/\s*,\s*/', trim($row->get($tagsField)), -1, PREG_SPLIT_NO_EMPTY);
            })->unique()->sort()->values();
            return response([
                'headers' => $headings[0][0],
                'rows' => ($preview ? $rows->first()->take($preview)->all() : []),
                'tags' => $tags->all(),
            ]);
        }

        if ($request->has('preset')) {
            $tags = $tags->concat(Preset::firstWhere('name', $request->get('preset'))->tags);
        }

        if ($request->has('tags')) {
            $tags = $tags->concat(Tag::whereIn('name', $request->get('tags'))->get());
        }

        Excel::import(new EntityImport($tags, $fields), $importFile);
    }
}
