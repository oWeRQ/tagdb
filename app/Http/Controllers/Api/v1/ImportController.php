<?php

namespace App\Http\Controllers\Api\v1;

use App\Tag;
use App\Preset;
use App\Imports\EntityImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function store(Request $request)
    {
        if ($request->has('preset')) {
            $tags = Preset::firstWhere('name', $request->get('preset'))->tags;
        } elseif ($request->has('tags')) {
            $tags = Tag::whereIn('name', preg_split('/\s*,\s*/', $request->get('tags')))->get();
        } else {
            abort(400);
        }

        Excel::import(new EntityImport($tags), $request->file('importFile'));
    }
}
