<?php

namespace App\Listeners;

use App\Events\ImportFailed;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendImportFailedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ImportFailed $event): void
    {
        // Send email notification
        $email = $event->email;
        $subject = 'Import failed notification';
        $message = "An error occurred during the import process.\n\n" .
                   "Import ID: {$event->importId}\n" .
                   "Error message: {$event->errorMessage}";

        Mail::raw($message, function ($mail) use ($email, $subject) {
            $mail->to($email)->subject($subject);
        });
    }
}
