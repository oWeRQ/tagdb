<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\v1\Tag;
use App\Models\v1\Field;
use App\Models\v1\Preset;
use App\Imports\EntityImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function store(Request $request)
    {
        $projectId = request()->project()->id;
        $importFile = $request->file('importFile');
        $tags = collect();
        $fields = collect($request->get('fields'))->map(function($field_id) {
            return is_numeric($field_id) ? Field::find($field_id) : $field_id;
        });

        if ($request->has('preview')) {
            $preview = $request->get('preview');
            $headings = (new HeadingRowImport)->toArray($importFile);
            $rows = Excel::toCollection(new EntityImport($tags, $fields, $projectId), $importFile)->first();
            $tags = EntityImport::collectionTags($rows, $fields);
            return response([
                'data' => [
                    'headers' => $headings[0][0],
                    'rows' => ($preview ? $rows->take($preview)->all() : []),
                    'tags' => $tags->pluck('name')->sort()->values()->all(),
                ],
            ]);
        }

        if ($request->has('preset')) {
            $tags = $tags->concat(Preset::firstWhere('name', $request->get('preset'))->tags);
        }

        if ($request->has('tags')) {
            $tags = $tags->concat(Tag::fromNames($request->get('tags')));
        }

        Excel::import(new EntityImport($tags, $fields, $projectId), $importFile);
    }
}
