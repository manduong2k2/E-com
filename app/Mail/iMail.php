<?php 
namespace App\Mail;

use Illuminate\Contracts\Mail\Mailable;

interface iMail extends Mailable{
    public function getUser();
}