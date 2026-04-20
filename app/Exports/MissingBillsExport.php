<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MissingBillsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;
    protected $columns = [];

    public function __construct($data)
    {
        $this->data = collect($data);

        // ✅ Extract column names dynamically
        if ($this->data->isNotEmpty()) {
            $this->columns = array_keys((array) $this->data->first());
        }
    }

    public function collection()
    {
        return $this->data;
    }

    // ✅ Auto headings
    public function headings(): array
    {
        return $this->columns;
    }

    // ✅ Map values in correct order
    public function map($row): array
    {
        return collect($row)->only($this->columns)->toArray();
    }
}
