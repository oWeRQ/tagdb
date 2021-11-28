<?php

namespace App\Exports;

use App\Field;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class EntitiesExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $query;
    protected $columns;
    protected $headings;

    public function __construct(Builder $query, array $columns)
    {
        $this->query = $query;
        $this->columns = $columns;
        $this->headings = [];

        foreach ($this->columns as $column) {
            $parts = explode('.', $column);
            if ($parts[0] === 'contents') {
                $field = Field::find($parts[1]);
                $query->selectContents($field->id);
                $this->headings[] = $field->name;
            } elseif ($column === 'tags') {
                $this->headings[] = 'Tags';
            } elseif ($column === 'name') {
                $this->headings[] = 'Name';
            } elseif ($column === 'created_at') {
                $this->headings[] = 'Created At';
            } elseif ($column === 'updated_at') {
                $this->headings[] = 'Updated At';
            } else {
                $this->headings[] = $column;
            }
        }
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function map($entity): array
    {
        return array_map(function($column) use($entity) {
            if ($column === 'tags')
                return $entity->tags->pluck('name')->join(', ');

            return $entity->$column;
        }, $this->columns);
    }
}
