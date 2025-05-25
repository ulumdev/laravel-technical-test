<?php

namespace App\Jobs;

use App\Models\ImportJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateImportJobStatus implements ShouldQueue
{
    use Queueable;

    protected $importJobId;
    protected $status;
    protected $message;
    protected $fileName;

    /**
     * Create a new job instance.
     */
    public function __construct($importJobId,  $status,  $message = null,  $fileName = null)
    {
        $this->importJobId = $importJobId;
        $this->status = $status;
        $this->message = $message;
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $importJob = ImportJob::find($this->importJobId);
        if ($importJob) {
            $importJob->status = $this->status;
            $importJob->message = $this->message;
            if ($this->fileName) {
                $importJob->file_name = $this->fileName;
            }
            $importJob->save();
        }
    }
}
