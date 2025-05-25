<?php

namespace App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

class DynamicExport implements FromCollection, WithHeadings, ShouldQueue
{

    use Exportable;

    protected $modelClass, $fields;

    public function __construct($modelClass, $fields)
    {
        $this->modelClass = $modelClass;
        $this->fields = $fields;
    }

    public function collection()
    {
        return ($this->modelClass)::select($this->fields)->get()->map(function ($item) {
            return collect($this->fields)->mapWithKeys(function ($f) use ($item) {
                $val = $item->{$f};
                return [$f => (is_array($val) || is_object($val)) ? json_encode($val) : $val];
            });
        });
    }

    public function headings(): array
    {
        return $this->fields;
    }
}

// class DynamicExport implements FromCollection, WithHeadings
// {
//     protected $model;
//     protected $fields;
//     protected $queryCallback;

//     public function __construct($model, $fields, $queryCallback = null)
//     {
//         $this->model = $model;
//         $this->fields = $fields;
//         $this->queryCallback = $queryCallback;
//     }

//     /**
//      * @return \Illuminate\Support\Collection
//      */
//     public function collection()
//     {
//         $query = ($this->model)::select($this->fields);
//         if ($this->queryCallback) {
//             $query = call_user_func($this->queryCallback, $query);
//         }
//         return $query->get();
//     }

//     public function headings(): array
//     {
//         return $this->fields;
//     }
// }
