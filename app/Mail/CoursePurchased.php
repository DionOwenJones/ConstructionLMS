<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Course;
use App\Models\Business;

class CoursePurchased extends Mailable
{
    use Queueable, SerializesModels;

    public $course;
    public $business;
    public $seats;

    public function __construct(Course $course, Business $business, int $seats)
    {
        $this->course = $course;
        $this->business = $business;
        $this->seats = $seats;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Course Purchase Confirmation: ' . $this->course->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.course-purchased',
        );
    }
}
