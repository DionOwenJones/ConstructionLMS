<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Course;

class CoursePurchaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $course;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
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
            ->subject('Course Purchase Confirmation')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Thank you for purchasing the course: ' . $this->course->title)
            ->line('You now have full access to all course materials.')
            ->action('Start Learning', route('courses.show', $this->course))
            ->line('If you have any questions, please don\'t hesitate to contact us.')
            ->line('Happy learning!');
    }
}
