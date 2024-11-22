<?php

namespace App\Console\Commands;

use App\Models\Certificate;
use App\Notifications\CertificateExpiringNotification;
use Illuminate\Console\Command;

class SendCertificateExpiryNotifications extends Command
{
    protected $signature = 'certificates:check-expiry';
    protected $description = 'Check for certificates that are about to expire and send notifications';

    public function handle()
    {
        $certificates = Certificate::with(['user', 'course'])
            ->whereHas('course', function($query) {
                $query->where('has_expiry', true);
            })
            ->get()
            ->filter(function($certificate) {
                $daysUntilExpiry = $certificate->course->getDaysUntilExpiry($certificate->created_at);
                return $daysUntilExpiry !== null && $daysUntilExpiry <= 30 && $daysUntilExpiry > 0;
            });

        foreach ($certificates as $certificate) {
            $daysUntilExpiry = $certificate->course->getDaysUntilExpiry($certificate->created_at);
            
            // Send notification if certificate expires in 30, 14, 7, or 1 days
            if (in_array($daysUntilExpiry, [30, 14, 7, 1])) {
                $certificate->user->notify(new CertificateExpiringNotification($certificate));
                $this->info("Sent expiry notification for certificate {$certificate->id} to user {$certificate->user->email}");
            }
        }

        $this->info('Certificate expiry check completed.');
    }
}
