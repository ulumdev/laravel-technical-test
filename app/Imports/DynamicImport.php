<?php

namespace App\Imports;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Str;

class DynamicImport implements ToCollection, WithHeadingRow, ShouldQueue, WithChunkReading
{
    use Queueable;

    protected $modelClass;
    protected $mapping;

    public function __construct($modelClass, $mapping)
    {
        $this->modelClass = $modelClass;
        $this->mapping = $mapping;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $data = [];
            foreach ($this->mapping as $dbField => $importField) {
                $value = $row[$importField] ?? null;

                // UUID
                if ($dbField === 'id') {
                    // Jika ada value dan valid UUID, pakai. Jika tidak, akan di-handle setelah loop.
                    if ($value && Str::isUuid($value)) {
                        $data['id'] = $value;
                    }
                    // Jangan continue di sini, supaya $data['id'] bisa diisi setelah loop jika belum ada
                    continue;
                }

                // JSON
                if ($dbField === 'details' && $value) {
                    if (is_array($value)) {
                        $data['details'] = json_encode($value);
                    } else {
                        $decoded = @json_decode($value, true);
                        $data['details'] = json_encode($decoded ?: []);
                    }
                    continue;
                }

                // Boolean
                if ($dbField === 'is_active') {
                    if (is_null($value)) {
                        $data['is_active'] = true;
                    } else {
                        if (is_string($value)) {
                            $low = strtolower($value);
                            $data['is_active'] = in_array($low, ['1', 'true', 'yes', 'y']);
                        } else {
                            $data['is_active'] = (bool)$value;
                        }
                    }
                    continue;
                }
                // Datetime
                if ($dbField === 'start_date' && $value) {
                    try {
                        $data['start_date'] = date('Y-m-d H:i:s', strtotime($value));
                    } catch (\Exception $e) {
                        $data['start_date'] = null;
                    }
                    continue;
                }

                $data[$dbField] = $value;
            }
            // FIX: Pastikan $data['id'] SELALU ADA sebelum create/update
            if (empty($data['id'])) {
                $data['id'] = (string) Str::uuid();
            }

            // updateOrCreate supaya tetap bisa update jika id sama
            ($this->modelClass)::updateOrCreate(['id' => $data['id']], $data);
        }
    }

    public function chunkSize(): int
    {
        return 1000; // Adjust chunk size as needed
    }
}
