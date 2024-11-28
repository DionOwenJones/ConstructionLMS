<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use App\Models\Course;
use Carbon\Carbon;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap.';

    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = SitemapGenerator::create(config('app.url'))
            ->hasCrawled(function (\Spatie\Sitemap\Crawler\Url $url) {
                // Set higher priority for course pages
                if ($url->segment(1) === 'courses') {
                    $url->setPriority(0.9)
                        ->setChangeFrequency('daily');
                }
                // Set medium priority for other public pages
                elseif (in_array($url->segment(1), ['', 'about', 'contact'])) {
                    $url->setPriority(0.8)
                        ->setChangeFrequency('weekly');
                }
                // Exclude admin and business routes
                elseif (in_array($url->segment(1), ['admin', 'business', 'login', 'register'])) {
                    return false;
                }
                // Default priority for other pages
                else {
                    $url->setPriority(0.5)
                        ->setChangeFrequency('monthly');
                }

                return $url;
            })
            ->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully.');
    }
}
