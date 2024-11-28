<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\User;
use Illuminate\Support\Carbon;

class CreateTestCourse extends Command
{
    protected $signature = 'course:create-test';
    protected $description = 'Create a test course with sections';

    public function handle()
    {
        // Get or create a test user
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => Carbon::now()
            ]
        );

        // If the user already existed, ensure they're verified
        if (!$user->wasRecentlyCreated && !$user->email_verified_at) {
            $user->email_verified_at = Carbon::now();
            $user->save();
        }

        // Create a test course
        $course = Course::create([
            'title' => 'Test Course',
            'description' => 'This is a test course to verify functionality.',
            'price' => 100, // Price in pence (£1)
            'featured' => false,
            'user_id' => $user->id,
        ]);

        // Create test sections
        $section1 = new CourseSection([
            'title' => 'Introduction',
            'content' => 'Welcome to the test course.',
            'order' => 1
        ]);

        $section2 = new CourseSection([
            'title' => 'Main Content',
            'content' => 'This is the main content section.',
            'order' => 2
        ]);

        // Save sections
        $course->sections()->saveMany([$section1, $section2]);

        // Add content blocks to sections
        $section1->contentBlocks()->create([
            'type' => 'text',
            'text_content' => '<h2>Welcome to the Course</h2><p>This is a test text block that demonstrates rich text content.</p>',
            'order' => 1
        ]);

        $section1->contentBlocks()->create([
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'video_title' => 'Introduction Video',
            'order' => 2
        ]);

        $section2->contentBlocks()->create([
            'type' => 'text',
            'text_content' => '<h2>Main Content Section</h2><p>This section contains important course material.</p>',
            'order' => 1
        ]);

        $section2->contentBlocks()->create([
            'type' => 'quiz',
            'quiz_data' => [
                'questions' => [
                    'What is the purpose of this course?',
                    'How many sections does this test course have?'
                ],
                'answers' => [
                    ['To test functionality', 'To learn', 'To have fun', 'All of the above'],
                    ['One', 'Two', 'Three', 'Four']
                ],
                'correct_answers' => [0, 1]
            ],
            'order' => 2
        ]);

        $this->info('Test course created successfully with ID: ' . $course->id);
        $this->info('Created sections with IDs: ' . $section1->id . ', ' . $section2->id);
        return 0;
    }
}
