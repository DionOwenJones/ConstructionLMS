<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CourseSection;
use App\Models\SectionContentBlock;

class CreateTestContentBlock extends Command
{
    protected $signature = 'content:create-test';
    protected $description = 'Create a test content block for the first section';

    public function handle()
    {
        // Get the first section
        $section = CourseSection::first();
        
        if (!$section) {
            $this->error('No sections found. Please create a section first.');
            return 1;
        }

        // Create a test content block
        $contentBlock = new SectionContentBlock([
            'type' => 'text',
            'content' => [
                'text' => '<h2>Test Content Block</h2><p>This is a test content block to verify that content blocks are working correctly.</p>'
            ],
            'order' => 1
        ]);

        $section->contentBlocks()->save($contentBlock);

        $this->info('Test content block created successfully for section: ' . $section->title);
        return 0;
    }
}
