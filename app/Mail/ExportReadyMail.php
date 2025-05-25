<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ExportReadyMail extends Mailable
{
    public $fileName;
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    public function build()
    {
        $url = url('/download-export?file=' . urlencode($this->fileName));
        return $this->subject('Export Project Selesai')
            ->view('emails.export_ready', ['url' => $url]);
    }
}
