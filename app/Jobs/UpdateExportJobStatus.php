<?php

namespace App\Jobs;

use App\Models\ExportJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateExportJobStatus implements ShouldQueue
{
    use Queueable;

    protected $exportJobId;
    protected $status;
    protected $fileName;
    /**
     * Create a new job instance.
     */
    public function __construct($exportJobId, $status, $fileName = null)
    {
        $this->exportJobId = $exportJobId;
        $this->status = $status;
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $exportJob = ExportJob::find($this->exportJobId);
        if ($exportJob) {
            $exportJob->status = $this->status;
            if ($this->fileName) {
                $exportJob->file_name = $this->fileName;
            }
            $exportJob->save();
        }
    }
}
