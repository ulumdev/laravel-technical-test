<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\DynamicExport;
use App\Imports\DynamicImport;
use App\Jobs\UpdateExportJobStatus;
use App\Jobs\UpdateImportJobStatus;
use App\Models\ExportJob;
use App\Models\ImportJob;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    protected $entityMap = [
        'project'    => \App\Models\Project::class,
        'task'       => \App\Models\Task::class,
        'attachment' => \App\Models\Attachment::class,
    ];

    protected function defaultFields($entity)
    {
        return match ($entity) {
            'project'    => ['id', 'name', 'details', 'is_active', 'start_date'],
            'task'       => ['id', 'project_id', 'title', 'metadata', 'is_done', 'due_date'],
            'attachment' => ['id', 'task_id', 'file_path', 'info', 'is_public', 'uploaded_at'],
            default      => ['id', 'name'],
        };
    }

    // Export (Async, pakai queue, polling, auto-download)
    public function export(Request $request, $entity)
    {
        $fields = $request->fields ?: $this->defaultFields($entity);
        if (is_string($fields)) $fields = array_map('trim', explode(',', $fields));
        $fileName = "{$entity}_" . Auth::id() . '_' . now()->timestamp . '.xlsx';

        $exportJob = ExportJob::create([
            'user_id'   => Auth::id(),
            'entity'    => $entity,
            'file_name' => $fileName,
            'status'    => 'processing',
        ]);

        // Export ke queue (disk 'exports'), update status jika sudah selesai
        (new DynamicExport($this->entityMap[$entity], $fields))
            ->queue($fileName, 'exports')
            ->chain(
                [
                    new UpdateExportJobStatus($exportJob->id, 'completed', $fileName)
                ]
            );

        return response()->json(['job_id' => $exportJob->id]);
    }

    // Ambil 10 job export terakhir user (untuk polling di frontend)
    public function exportJobs(Request $request)
    {
        $jobs = ExportJob::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(10)->get();

        return response()->json($jobs);
    }

    public function downloadExport(Request $request)
    {
        $job = ExportJob::where('id', $request->query('job_id'))
            ->where('user_id', Auth::id())
            ->whereIn('status', ['done', 'completed'])
            ->firstOrFail();

        // File disimpan di disk 'exports' (storage/app/exports)
        $filePath = storage_path('app/exports/' . $job->file_name);
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath);
    }

    public function import(Request $request, $entity)
    {
        $request->validate([
            'file'    => 'required|file|mimes:xlsx,xls,csv',
            'mapping' => 'required',
        ]);

        // Parsing mapping json jika string
        $mapping = $request->mapping;
        if (is_string($mapping)) {
            $mapping = json_decode($mapping, true);
        }
        if (!is_array($mapping)) {
            return response()->json(['message' => 'Mapping tidak valid'], 422);
        }

        $file = $request->file('file');
        $fileName = $entity . '_' . Auth::id() . '_' . now()->timestamp . '.' . $file->getClientOriginalExtension();
        // $path = $file->storeAs('imports', $fileName);

        // Simpan file ke disk 'imports' (storage/app/imports)
        Storage::disk('imports')->putFileAs(
            '',
            $file,
            $fileName
        );
        // Hanya nama file (relatif terhadap disk 'imports')
        $path = $fileName;

        $importJob = ImportJob::create([
            'user_id'   => Auth::id(),
            'entity'    => $entity,
            'file_name' => $fileName,
            'status'    => 'processing',
        ]);

        // Import via queue, gunakan Excel::queueImport
        Excel::queueImport(
            new DynamicImport($this->entityMap[$entity], $mapping),
            $path,
            'imports'
        )->chain(
            [
                new UpdateImportJobStatus($importJob->id, 'completed', 'Import completed', $fileName)
            ]
        );

        return response()->json(['job_id' => $importJob->id]);
    }

    public function importJobs(Request $request)
    {
        $jobs = ImportJob::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(10)->get();

        return response()->json($jobs);
    }
}
