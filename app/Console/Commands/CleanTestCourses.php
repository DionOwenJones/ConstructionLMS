<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use App\Models\ContentBlock;
use App\Models\CourseSection;
use Illuminate\Support\Facades\DB;

class CleanTestCourses extends Command
{
    protected $signature = 'course:clean-test';
    protected $description = 'Clean up test courses and their content';

    public function handle()
    {
        $this->info('Cleaning up test courses...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Delete all content blocks first
        ContentBlock::truncate();
        $this->info('Content blocks cleared.');

        // Delete all sections
        CourseSection::truncate();
        $this->info('Course sections cleared.');

        // Delete all courses
        Course::truncate();
        $this->info('Courses cleared.');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('Cleanup completed successfully.');
        return 0;
    }
}
