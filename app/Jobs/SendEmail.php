<?php

namespace App\Jobs;

use App\Mail\iMail;
use App\Mail\OrderPlacedMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $mail;
    /**
     * Create a new job instance.
     */
    public function __construct(iMail $mail)
    {
        $this->mail=$mail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->mail->getUser())->send($this->mail);
    }
}
