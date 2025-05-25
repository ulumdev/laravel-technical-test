<?php

namespace App\Imports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class DynamicImport implements ToModel, WithHeadingRow, ShouldQueue
{
    use Importable;

    protected $modelClass, $mapping;

    public function __construct($modelClass, $mapping)
    {
        $this->modelClass = $modelClass;
        $this->mapping = $mapping;
    }

    public function model(array $row)
    {
        $attributes = [];
        foreach ($this->mapping as $excelCol => $dbCol) {
            $attributes[$dbCol] = $row[$excelCol] ?? null;
        }
        // Bisa updateOrCreate jika id dipakai
        if (isset($attributes['id'])) {
            return ($this->modelClass)::updateOrCreate(['id' => $attributes['id']], $attributes);
        } else {
            return new ($this->modelClass)($attributes);
        }
    }
}
