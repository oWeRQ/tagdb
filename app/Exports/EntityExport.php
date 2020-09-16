<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class EntitiesExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $query;
    protected $columns;

    public function __construct($query, $columns)
    {
        $this->query = $query;
        $this->columns = $columns;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Created At',
        ];
    }

    public function map($entity): array
    {
        return [
            $entity->name,
            $entity->created_at,
        ];
    }

    public function query()
    {
        return $this->query;
    }
}
