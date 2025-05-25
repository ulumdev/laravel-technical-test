<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportToExcel implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    protected $modelClass, $fields, $userId, $entity;

    /**
     * Create a new job instance.
     */
    public function __construct($modelClass, $fields, $userId, $entity)
    {
        $this->modelClass = $modelClass;
        $this->fields = $fields;
        $this->userId = $userId;
        $this->entity = $entity;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fields = $this->fields;
        if (is_string($fields)) {
            $fields = array_map('trim', explode(',', $fields));
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($fields, null, 'A1');

        $rows = ($this->modelClass)::select($fields)->get()->map(function ($item) use ($fields) {
            return array_map(function ($f) use ($item) {
                $val = $item->{$f};
                return is_array($val) || is_object($val) ? json_encode($val) : $val;
            }, $fields);
        })->toArray();

        if (is_array($rows) && count($rows)) {
            $sheet->fromArray($rows, null, 'A2');
        }

        $fileName = "exports/{$this->entity}_" . $this->userId . '_' . now()->timestamp . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/' . $fileName));
    }
}
