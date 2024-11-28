<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Course;
use Carbon\Carbon;

class UncompletedCourseReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $courses;
    protected $enrollmentDates;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $courses, array $enrollmentDates)
    {
        $this->courses = $courses;
        $this->enrollmentDates = $enrollmentDates;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject('Reminder: You have uncompleted courses')
            ->greeting('Hello ' . $notifiable->name)
            ->line('This is a friendly reminder about your uncompleted courses:');

        foreach ($this->courses as $index => $course) {
            $enrolledFor = Carbon::parse($this->enrollmentDates[$index])->diffForHumans();
            $mailMessage->line("- {$course->title} (Enrolled {$enrolledFor})");
        }

        return $mailMessage
            ->line('Please log in to continue your learning journey.')
            ->action('Go to My Courses', url('/dashboard/courses'))
            ->line('Thank you for using our platform!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'courses' => $this->courses,
            'enrollment_dates' => $this->enrollmentDates
        ];
    }
}
