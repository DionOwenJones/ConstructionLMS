<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseSection;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $courses = [
            [
                'title' => 'Advanced Project Management',
                'description' => 'Master advanced project management techniques, risk assessment, and agile methodologies.',
                'sections' => ['Project Initiation', 'Risk Management', 'Agile Methodologies', 'Stakeholder Management']
            ],
            [
                'title' => 'Cybersecurity Fundamentals',
                'description' => 'Learn essential cybersecurity concepts, threat detection, and security best practices.',
                'sections' => ['Security Basics', 'Threat Detection', 'Network Security', 'Security Protocols']
            ],
            [
                'title' => 'Data Analytics Essentials',
                'description' => 'Comprehensive introduction to data analytics, visualization, and interpretation.',
                'sections' => ['Data Collection', 'Data Cleaning', 'Data Visualization', 'Data Interpretation']
            ],
            [
                'title' => 'Leadership and Team Management',
                'description' => 'Develop essential leadership skills and learn effective team management strategies.',
                'sections' => ['Leadership Fundamentals', 'Team Building', 'Conflict Resolution', 'Performance Management']
            ],
            [
                'title' => 'Digital Marketing Strategy',
                'description' => 'Master modern digital marketing techniques and campaign management.',
                'sections' => ['Marketing Fundamentals', 'Social Media Strategy', 'Content Marketing', 'Analytics & ROI']
            ]
        ];

        foreach ($courses as $courseData) {
            $course = Course::create([
                'title' => $courseData['title'],
                'description' => $courseData['description'],
                'image' => 'courses/default.jpg',
                'price' => rand(4900, 9900),
                'status' => 'published'
            ]);

            foreach ($courseData['sections'] as $index => $sectionTitle) {
                CourseSection::create([
                    'course_id' => $course->id,
                    'title' => $sectionTitle,
                    'order' => $index + 1,
                    'content' => 'Content for ' . $sectionTitle,
                    'video_url' => 'https://example.com/video.mp4'
                ]);
            }
        }
    }
}
