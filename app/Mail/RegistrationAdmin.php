<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegistrationAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $webinar;
    public $registration;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(\App\WebinarRegistration $registration)
    {
        $this->registration   = $registration;
        $this->user           = \App\User::find($registration->user_id);
        $this->webinar        = $this->registration->webinar;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.registration-admin');
    }
}
