<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ImportSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $import;

    /**
     * Create a new message instance.
     *
     * @param $import
     */
    public function __construct($import)
    {
        $this->import = $import;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Import Completed Successfully')
                    ->view('emails.import_success')
                    ->with([
                        'fileName' => $this->import->file_name,
                        'status' => $this->import->status,
                    ]);
    }
}