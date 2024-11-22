<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $certificate;

    public function __construct(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $daysUntilExpiry = $this->certificate->course->getDaysUntilExpiry($this->certificate->created_at);
        $courseUrl = route('courses.show', $this->certificate->course);

        return (new MailMessage)
            ->subject('Your Course Certificate is Expiring Soon')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your certificate for the course "' . $this->certificate->course->title . '" will expire in ' . $daysUntilExpiry . ' days.')
            ->line('To maintain your certification, you will need to retake the course before it expires.')
            ->action('Retake Course', $courseUrl)
            ->line('Thank you for using our platform!');
    }
}
