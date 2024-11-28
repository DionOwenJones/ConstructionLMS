<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Course;
use App\Models\Business;

class CourseAllocationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $course;
    protected $business;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course, Business $business)
    {
        $this->course = $course;
        $this->business = $business;
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
        return (new MailMessage)
            ->subject('New Course Access Granted')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->business->name . ' has granted you access to the following course:')
            ->line($this->course->title)
            ->line('You now have full access to all course materials.')
            ->action('Start Learning', route('courses.show', $this->course))
            ->line('If you have any questions, please contact your administrator.')
            ->line('Happy learning!');
    }
}
