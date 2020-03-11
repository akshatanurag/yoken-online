<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Enrollment extends Mailable
{
    use Queueable, SerializesModels;

    public $enrollment;
    public $batch;
    public $course;
    public $institute;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(\App\Enrollment $enrollment)
    {
        $this->enrollment   = $enrollment;
        $this->batch        = \App\Batch::find($this->enrollment->batch_id);
        $this->course       = \App\Course::find($this->batch->course_id);
        $this->institute    = \App\Institute::find($this->course->institute_id);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.enrollment');
    }
}
