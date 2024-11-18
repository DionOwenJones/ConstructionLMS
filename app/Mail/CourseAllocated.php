<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Course;
use App\Models\User;

class CourseAllocated extends Mailable
{
    use Queueable, SerializesModels;

    public $course;
    public $user;
    public $businessName;

    public function __construct(Course $course, User $user, string $businessName)
    {
        $this->course = $course;
        $this->user = $user;
        $this->businessName = $businessName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Course Allocated: ' . $this->course->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.course-allocated',
        );
    }
}
