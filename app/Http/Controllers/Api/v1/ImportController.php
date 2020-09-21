<?php

namespace App\Http\Controllers\Api\v1;

use App\Entity;
use App\Preset;
use App\Imports\EntityImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->has('preset')) {
            return 400;
        }

        $preset = Preset::firstWhere('name', $request->get('preset'));

        Excel::import(new EntityImport($preset->tags), $request->file('importFile'));
    }
}
