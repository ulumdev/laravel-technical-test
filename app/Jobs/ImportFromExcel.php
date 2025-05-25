<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\IOFactory;


class ImportFromExcel implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    protected $modelClass, $filePath, $mapping;

    /**
     * Create a new job instance.
     */
    public function __construct($modelClass, $filePath, $mapping)
    {
        $this->modelClass = $modelClass;
        $this->filePath = $filePath;
        $this->mapping = $mapping;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $spreadsheet = IOFactory::load(storage_path('app/' . $this->filePath));
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);
        array_shift($rows); // header

        foreach ($rows as $row) {
            $attributes = [];
            foreach ($this->mapping as $excelCol => $dbCol) {
                $attributes[$dbCol] = $row[$excelCol] ?? null;
            }
            if (array_filter($attributes)) {
                ($this->modelClass)::updateOrCreate(['id' => $attributes['id'] ?? null], $attributes);
            }
        }
    }
}
