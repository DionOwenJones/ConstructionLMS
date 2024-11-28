<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\SectionContentBlock;
use App\Models\User;
use App\Models\Business;
use App\Models\BusinessEmployee;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CourseSeeder extends Seeder
{
    public function run()
    {
        // Create some admin users
        $admins = [
            [
                'name' => 'Main Admin',
                'email' => 'admin@constructionlms.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now()
            ],
            [
                'name' => 'Secondary Admin',
                'email' => 'admin2@constructionlms.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now()
            ]
        ];

        foreach ($admins as $adminData) {
            User::firstOrCreate(
                ['email' => $adminData['email']],
                $adminData
            );
        }

        // Create some businesses
        $businesses = [
            [
                'name' => 'Construction Masters Ltd',
                'email' => 'info@constructionmasters.com',
                'phone' => '0123456789',
                'address' => '123 Builder Street, Construction City',
                'user' => [
                    'name' => 'John Builder',
                    'email' => 'john@constructionmasters.com',
                    'password' => Hash::make('password'),
                    'role' => 'business',
                    'email_verified_at' => now()
                ]
            ],
            [
                'name' => 'Safety First Construction',
                'email' => 'info@safetyfirst.com',
                'phone' => '9876543210',
                'address' => '456 Safety Road, Builder Town',
                'user' => [
                    'name' => 'Sarah Safety',
                    'email' => 'sarah@safetyfirst.com',
                    'password' => Hash::make('password'),
                    'role' => 'business',
                    'email_verified_at' => now()
                ]
            ],
            [
                'name' => 'Modern Builders Inc',
                'email' => 'contact@modernbuilders.com',
                'phone' => '5555555555',
                'address' => '789 Modern Avenue, New City',
                'user' => [
                    'name' => 'Mike Modern',
                    'email' => 'mike@modernbuilders.com',
                    'password' => Hash::make('password'),
                    'role' => 'business',
                    'email_verified_at' => now()
                ]
            ]
        ];

        // Create businesses and their owners
        foreach ($businesses as $businessData) {
            $userData = $businessData['user'];
            unset($businessData['user']);

            // Create business owner
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Create business
            $business = Business::firstOrCreate(
                ['email' => $businessData['email']],
                [
                    'name' => $businessData['name'],
                    'user_id' => $user->id,
                    'email' => $businessData['email']
                ]
            );

            // Create some employees (regular users) for each business
            $employees = [
                [
                    'name' => "Employee 1 " . $business->name,
                    'email' => "employee1@" . Str::slug($business->name) . ".com",
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'email_verified_at' => now()
                ],
                [
                    'name' => "Employee 2 " . $business->name,
                    'email' => "employee2@" . Str::slug($business->name) . ".com",
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'email_verified_at' => now()
                ]
            ];

            foreach ($employees as $employeeData) {
                $employee = User::firstOrCreate(
                    ['email' => $employeeData['email']],
                    $employeeData
                );

                // Associate employee with business through BusinessEmployee model
                BusinessEmployee::firstOrCreate([
                    'business_id' => $business->id,
                    'user_id' => $employee->id
                ]);
            }
        }

        // Create some regular users
        $users = [
            [
                'name' => 'Regular User 1',
                'email' => 'user1@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now()
            ],
            [
                'name' => 'Regular User 2',
                'email' => 'user2@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now()
            ],
            [
                'name' => 'Regular User 3',
                'email' => 'user3@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now()
            ]
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $courses = [
            [
                'title' => 'Advanced Construction Safety',
                'description' => 'Comprehensive training on modern construction safety protocols, risk management, and emergency procedures.',
                'price' => 24900,
                'featured' => true,
                'created_at' => Carbon::now()->subDays(7),
                'sections' => [
                    [
                        'title' => 'Introduction to Construction Safety',
                        'content' => 'Introduction to construction safety',
                        'order' => 1,
                        'blocks' => [
                            [
                                'type' => 'video',
                                'content' => [
                                    'url' => 'https://www.youtube.com/watch?v=3C7bwPzpVPo',
                                    'title' => 'Introduction to Construction Safety'
                                ],
                                'order' => 1
                            ],
                            [
                                'type' => 'text',
                                'content' => [
                                    'text' => 'Welcome to the Construction Safety course. This introductory section covers the fundamental principles of construction site safety. You will learn about the importance of safety culture, basic safety concepts, and how to identify common workplace hazards. Understanding these basics is crucial for maintaining a safe work environment and preventing accidents on construction sites.'
                                ],
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'Personal Protective Equipment (PPE)',
                        'content' => 'Understanding personal protective equipment (PPE)',
                        'order' => 2,
                        'blocks' => [
                            [
                                'type' => 'video',
                                'content' => [
                                    'url' => 'https://www.youtube.com/watch?v=Iq6PEpCZcXo',
                                    'title' => 'Personal Protective Equipment (PPE) Overview'
                                ],
                                'order' => 1
                            ],
                            [
                                'type' => 'text',
                                'content' => [
                                    'text' => 'Personal Protective Equipment (PPE) is your first line of defense against workplace hazards. This section details the various types of PPE required on construction sites, including hard hats, safety boots, high-visibility clothing, eye protection, hearing protection, and respiratory equipment. Learn how to properly select, use, maintain, and store your PPE to ensure maximum protection.'
                                ],
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'Site Safety Protocols',
                        'content' => 'Understanding site safety protocols',
                        'order' => 3,
                        'blocks' => [
                            [
                                'type' => 'video',
                                'content' => [
                                    'url' => 'https://www.youtube.com/watch?v=y3yRv5Jg5TI',
                                    'title' => 'Site Safety Protocols'
                                ],
                                'order' => 1
                            ],
                            [
                                'type' => 'text',
                                'content' => [
                                    'text' => 'Site safety protocols are essential procedures that ensure everyone on the construction site works safely and efficiently. This section covers key protocols including site access control, safety signage, communication systems, toolbox talks, and safety meetings. You will also learn about proper documentation and reporting procedures for safety-related incidents and near-misses.'
                                ],
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'Emergency Response Procedures',
                        'content' => 'Emergency response procedures',
                        'order' => 4,
                        'blocks' => [
                            [
                                'type' => 'video',
                                'content' => [
                                    'url' => 'https://www.youtube.com/watch?v=8tRid0Xg3dY',
                                    'title' => 'Emergency Response Procedures'
                                ],
                                'order' => 1
                            ],
                            [
                                'type' => 'text',
                                'content' => [
                                    'text' => 'Being prepared for emergencies is crucial in construction. This section outlines emergency response procedures, including evacuation plans, first aid protocols, and emergency communication systems. Learn how to respond to various types of emergencies such as fires, structural collapses, medical emergencies, and severe weather conditions. Understanding these procedures could save lives in critical situations.'
                                ],
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'Risk Assessment Methods',
                        'content' => 'Risk assessment methods',
                        'order' => 5,
                        'blocks' => [
                            [
                                'type' => 'video',
                                'content' => [
                                    'url' => 'https://www.youtube.com/watch?v=PZmNZi8bon8',
                                    'title' => 'Risk Assessment Methods'
                                ],
                                'order' => 1
                            ],
                            [
                                'type' => 'text',
                                'content' => [
                                    'text' => 'Risk assessment is a systematic process of evaluating potential hazards and implementing control measures. This section teaches you how to identify, analyze, and evaluate risks on construction sites. Learn about the hierarchy of controls, risk matrices, and how to develop effective risk management plans. You will also understand how to conduct regular risk assessments and update safety measures accordingly.'
                                ],
                                'order' => 2
                            ]
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Sustainable Construction Methods',
                'description' => 'Learn about eco-friendly construction techniques, materials, and LEED certification requirements.',
                'price' => 29900,
                'featured' => true,
                'created_at' => Carbon::now()->subDays(14),
                'sections' => [
                    [
                        'title' => 'Green Building Materials',
                        'content' => 'Introduction to sustainable building materials',
                        'order' => 1,
                        'blocks' => [
                            [
                                'type' => 'video',
                                'content' => [
                                    'url' => 'https://www.youtube.com/watch?v=6ZUxrYxY32k',
                                    'title' => 'Sustainable Building Materials'
                                ],
                                'order' => 1
                            ],
                            [
                                'type' => 'text',
                                'content' => [
                                    'text' => 'Sustainable building materials are essential for reducing the environmental impact of construction projects. This section introduces you to various eco-friendly materials, including recycled materials, low-VOC paints, sustainable wood products, and energy-efficient insulation. Learn how to select and specify these materials for your construction projects.'
                                ],
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'Energy Efficiency',
                        'content' => 'Energy-efficient construction methods',
                        'order' => 2,
                        'blocks' => [
                            [
                                'type' => 'video',
                                'content' => [
                                    'url' => 'https://www.youtube.com/watch?v=3oZmZhqgQ1E',
                                    'title' => 'Energy Efficient Construction'
                                ],
                                'order' => 1
                            ],
                            [
                                'type' => 'text',
                                'content' => [
                                    'text' => 'Energy efficiency is a critical aspect of sustainable construction. This section covers various techniques for reducing energy consumption in buildings, including passive solar design, insulation, windows, and HVAC systems. Learn how to design and build energy-efficient buildings that minimize environmental impact.'
                                ],
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'Water Conservation',
                        'content' => 'Water-saving techniques in construction',
                        'order' => 3,
                        'blocks' => [
                            [
                                'type' => 'video',
                                'content' => [
                                    'url' => 'https://www.youtube.com/watch?v=gB8yu0P1mXI',
                                    'title' => 'Water Conservation in Construction'
                                ],
                                'order' => 1
                            ],
                            [
                                'type' => 'text',
                                'content' => [
                                    'text' => 'Water conservation is essential for reducing the environmental impact of construction projects. This section covers various techniques for conserving water in construction, including low-flow fixtures, greywater reuse systems, and rainwater harvesting. Learn how to design and implement water-saving measures in your construction projects.'
                                ],
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'Waste Management',
                        'content' => 'Construction waste reduction strategies',
                        'order' => 4,
                        'blocks' => [
                            [
                                'type' => 'video',
                                'content' => [
                                    'url' => 'https://www.youtube.com/watch?v=wWxqJZ-c_YM',
                                    'title' => 'Construction Waste Management'
                                ],
                                'order' => 1
                            ],
                            [
                                'type' => 'text',
                                'content' => [
                                    'text' => 'Construction waste management is critical for reducing the environmental impact of construction projects. This section covers various strategies for reducing waste, including recycling, reusing materials, and minimizing packaging. Learn how to develop effective waste management plans and implement sustainable waste reduction practices.'
                                ],
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'LEED Certification',
                        'content' => 'Understanding LEED requirements',
                        'order' => 5,
                        'blocks' => [
                            [
                                'type' => 'video',
                                'content' => [
                                    'url' => 'https://www.youtube.com/watch?v=B8j6wq_5mZw',
                                    'title' => 'LEED Certification Process'
                                ],
                                'order' => 1
                            ],
                            [
                                'type' => 'text',
                                'content' => [
                                    'text' => 'LEED (Leadership in Energy and Environmental Design) certification is a widely recognized standard for sustainable buildings. This section covers the LEED certification process, including the different levels of certification, credit categories, and documentation requirements. Learn how to navigate the LEED certification process and achieve certification for your construction projects.'
                                ],
                                'order' => 2
                            ]
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Construction Project Management Essentials',
                'description' => 'Master the fundamentals of construction project management, from planning to execution.',
                'price' => 19900,
                'featured' => false,
                'created_at' => Carbon::now()->subDays(21),
                'sections' => [
                    [
                        'title' => 'Project Planning',
                        'content' => 'Essential project planning techniques',
                        'order' => 1,
                        'blocks' => [
                            [
                                'type' => 'video',
                                'content' => [
                                    'url' => 'https://www.youtube.com/watch?v=iOALWm-kCaM',
                                    'title' => 'Construction Project Planning'
                                ],
                                'order' => 1
                            ],
                            [
                                'type' => 'text',
                                'content' => [
                                    'text' => 'Project planning is a critical phase of construction project management. This section covers essential project planning techniques, including project scope definition, work breakdown structures, scheduling, and budgeting. Learn how to develop effective project plans and set your projects up for success.'
                                ],
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'Resource Management',
                        'content' => 'Managing construction resources effectively',
                        'order' => 2,
                        'blocks' => [
                            [
                                'type' => 'video',
                                'content' => [
                                    'url' => 'https://www.youtube.com/watch?v=8W9qbS-dZ2c',
                                    'title' => 'Construction Resource Management'
                                ],
                                'order' => 1
                            ],
                            [
                                'type' => 'text',
                                'content' => [
                                    'text' => 'Resource management is critical for ensuring that construction projects are completed on time and within budget. This section covers various techniques for managing construction resources, including labor, materials, equipment, and subcontractors. Learn how to develop effective resource management plans and optimize resource allocation.'
                                ],
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'Schedule Management',
                        'content' => 'Construction scheduling techniques',
                        'order' => 3,
                        'blocks' => [
                            [
                                'type' => 'video',
                                'content' => [
                                    'url' => 'https://www.youtube.com/watch?v=jM8XgGZXP2k',
                                    'title' => 'Construction Schedule Management'
                                ],
                                'order' => 1
                            ],
                            [
                                'type' => 'text',
                                'content' => [
                                    'text' => 'Schedule management is critical for ensuring that construction projects are completed on time. This section covers various construction scheduling techniques, including Gantt charts, critical path method, and resource leveling. Learn how to develop effective project schedules and manage project timelines.'
                                ],
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'Quality Control',
                        'content' => 'Quality management in construction',
                        'order' => 4,
                        'blocks' => [
                            [
                                'type' => 'video',
                                'content' => [
                                    'url' => 'https://www.youtube.com/watch?v=TQPvqR2ykRU',
                                    'title' => 'Construction Quality Control'
                                ],
                                'order' => 1
                            ],
                            [
                                'type' => 'text',
                                'content' => [
                                    'text' => 'Quality control is critical for ensuring that construction projects meet the required standards. This section covers various quality management techniques, including quality planning, quality assurance, and quality control. Learn how to develop effective quality management plans and ensure that your construction projects meet the required standards.'
                                ],
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'Project Closeout',
                        'content' => 'Project completion and handover procedures',
                        'order' => 5,
                        'blocks' => [
                            [
                                'type' => 'video',
                                'content' => [
                                    'url' => 'https://www.youtube.com/watch?v=9uOMectkCCs',
                                    'title' => 'Construction Project Closeout'
                                ],
                                'order' => 1
                            ],
                            [
                                'type' => 'text',
                                'content' => [
                                    'text' => 'Project closeout is a critical phase of construction project management. This section covers various project completion and handover procedures, including final inspections, punch lists, and project evaluations. Learn how to ensure that your construction projects are completed successfully and meet the required standards.'
                                ],
                                'order' => 2
                            ]
                        ]
                    ]
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

            foreach ($courseData['sections'] as $sectionData) {
                $section = CourseSection::create([
                    'course_id' => $course->id,
                    'title' => $sectionData['title'],
                    'content' => $sectionData['content'],
                    'order' => $sectionData['order']
                ]);

                foreach ($sectionData['blocks'] as $blockData) {
                    $contentBlockData = [
                        'section_id' => $section->id,
                        'type' => $blockData['type'],
                        'order' => $blockData['order']
                    ];

                    // Add content based on type
                    if ($blockData['type'] === 'video') {
                        $contentBlockData['video_url'] = $blockData['content']['url'];
                        $contentBlockData['video_title'] = $blockData['content']['title'];
                    } elseif ($blockData['type'] === 'text') {
                        $contentBlockData['text_content'] = $blockData['content']['text'];
                    }

                    SectionContentBlock::create($contentBlockData);
                }
            }

            // Add some enrollments for featured courses
            if ($featured) {
                // Create some regular users
                $users = User::factory()->count(5)->create(['role' => 'user']);
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