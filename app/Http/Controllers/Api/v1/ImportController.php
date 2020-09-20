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
        Excel::import(new EntityImport, $request->file('importFile'));
    }
}
