<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Institute extends Mailable
{
    use Queueable, SerializesModels;

    public $institute;
    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(\App\Institute $institute, $password)
    {
        $this->institute = $institute;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.institute');
    }
}
