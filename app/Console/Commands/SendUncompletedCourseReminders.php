<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\UncompletedCourseReminder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SendUncompletedCourseReminders extends Command
{
    protected $signature = 'reminders:uncompleted-courses';
    protected $description = 'Send reminders to users about their uncompleted courses';

    public function handle()
    {
        $this->info('Starting to send uncompleted course reminders...');

        // Get all users with uncompleted courses
        $users = User::whereHas('courses', function ($query) {
            $query->where('completed', false)
                ->where('course_user.created_at', '<=', now()->subWeek());
        })->get();

        $count = 0;
        foreach ($users as $user) {
            // Get user's uncompleted courses with enrollment dates
            $coursesWithDates = DB::table('course_user')
                ->join('courses', 'course_user.course_id', '=', 'courses.id')
                ->where('course_user.user_id', $user->id)
                ->where('course_user.completed', false)
                ->where('course_user.created_at', '<=', now()->subWeek())
                ->select([
                    'courses.*',
                    'course_user.created_at as enrolled_at'
                ])
                ->get();

            if ($coursesWithDates->isNotEmpty()) {
                $courses = $coursesWithDates->map(function($item) {
                    return (object)[
                        'id' => $item->id,
                        'title' => $item->title
                    ];
                })->all();
                
                $enrollmentDates = $coursesWithDates->pluck('enrolled_at')->all();

                $user->notify(new UncompletedCourseReminder($courses, $enrollmentDates));
                $count++;
            }
        }

        $this->info("Sent reminders to {$count} users.");
    }
}
