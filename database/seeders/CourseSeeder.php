<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $courses = [
            [
                'title' => 'Construction Safety Fundamentals',
                'description' => 'Essential safety practices and protocols for construction sites. Learn about PPE, hazard identification, and emergency procedures.',
                'price' => 19900,
                'featured' => true,
                'created_at' => Carbon::now()->subDays(7), // New course
                'sections' => [
                    'Personal Protective Equipment (PPE)',
                    'Hazard Identification and Risk Assessment',
                    'Emergency Response Procedures',
                    'Safety Documentation and Reporting'
                ]
            ],
            [
                'title' => 'Heavy Equipment Operation',
                'description' => 'Comprehensive training on operating construction heavy equipment safely and efficiently. Covers excavators, bulldozers, and cranes.',
                'price' => 29900,
                'featured' => true,
                'created_at' => Carbon::now()->subDays(30),
                'sections' => [
                    'Equipment Safety Basics',
                    'Excavator Operation',
                    'Bulldozer Techniques',
                    'Crane Safety and Operation'
                ]
            ],
            [
                'title' => 'Construction Project Management',
                'description' => 'Learn to manage construction projects effectively, from planning to completion. Includes budgeting, scheduling, and team management.',
                'price' => 24900,
                'featured' => true,
                'created_at' => Carbon::now()->subDays(5), // New course
                'sections' => [
                    'Project Planning and Scheduling',
                    'Budget Management',
                    'Team Coordination',
                    'Quality Control'
                ]
            ],
            [
                'title' => 'Building Code Compliance',
                'description' => 'Stay up-to-date with building codes and regulations. Essential knowledge for contractors and construction managers.',
                'price' => 14900,
                'created_at' => Carbon::now()->subDays(60),
                'sections' => [
                    'Code Basics and Updates',
                    'Inspection Procedures',
                    'Documentation Requirements',
                    'Compliance Strategies'
                ]
            ],
            [
                'title' => 'Sustainable Construction Practices',
                'description' => 'Learn about eco-friendly construction methods, materials, and certifications. Essential for modern construction projects.',
                'price' => 19900,
                'created_at' => Carbon::now()->subDays(10), // New course
                'sections' => [
                    'Green Building Materials',
                    'Energy Efficiency',
                    'Waste Management',
                    'LEED Certification'
                ]
            ]
        ];

        // Get the admin user
        $admin = User::where('role', 'admin')->first();

        foreach ($courses as $courseData) {
            $createdAt = $courseData['created_at'] ?? Carbon::now();
            $featured = $courseData['featured'] ?? false;

            $course = Course::create([
                'title' => $courseData['title'],
                'description' => $courseData['description'],
                'price' => $courseData['price'],
                'status' => 'published',
                'featured' => $featured,
                'image' => null,
                'user_id' => $admin->id,
                'slug' => Str::slug($courseData['title']),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'published_at' => $createdAt
            ]);

            // Create sections for each course
            $order = 1;
            foreach ($courseData['sections'] as $sectionTitle) {
                CourseSection::create([
                    'course_id' => $course->id,
                    'title' => $sectionTitle,
                    'content' => "# {$sectionTitle}\n\nDetailed content for {$sectionTitle} will be covered in this section.",
                    'order' => $order++
                ]);
            }

            // Add some enrollments for featured courses
            if ($featured) {
                // Create some regular users
                $users = User::factory()->count(15)->create(['role' => 'user']);
                foreach ($users as $user) {
                    $course->users()->attach($user->id, [
                        'enrolled_at' => now(),
                        'completed_sections' => '[]',
                        'completed_sections_count' => 0,
                        'last_accessed_at' => now()
                    ]);
                }
            }
        }
    }
}