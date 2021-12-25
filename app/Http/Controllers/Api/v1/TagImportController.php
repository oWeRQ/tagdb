<?php

namespace App\Http\Controllers\Api\v1;

use App\Tag;
use App\Field;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class TagImportController extends Controller
{
    public function store(Request $request)
    {
        $importFile = $request->file('importFile');
        $importTags = json_decode($importFile->get(), true);

        foreach ($importTags as $importTag) {
            $tag = Tag::updateOrCreate([
                'name' => $importTag['name'],
            ], $importTag);
            foreach ($importTag['fields'] as $importField) {
                Field::updateOrCreate([
                    'tag_id' => $tag->id,
                    'name' => $importField['name'],
                ], [
                    'type' => $importField['type'],
                    'code' => $importField['code'],
                ]);
            }
        }

        return response()->noContent();
    }
}
