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
            return Field::find($field_id);
        });

        if ($request->has('preview')) {
            $preview = $request->get('preview');
            $headings = (new HeadingRowImport)->toArray($importFile);
            $rows = Excel::toCollection(new EntityImport($tags, $fields), $importFile);
            return response([
                'headers' => $headings[0][0],
                'rows' => ($preview ? $rows->take($preview)->all() : []),
            ]);
        }

        if ($request->has('preset')) {
            $tags = Preset::firstWhere('name', $request->get('preset'))->tags;
        } elseif ($request->has('tags')) {
            $tags = Tag::whereIn('name', preg_split('/\s*,\s*/', $request->get('tags')))->get();
        } else {
            abort(400);
        }

        Excel::import(new EntityImport($tags, $fields), $importFile);
    }
}
