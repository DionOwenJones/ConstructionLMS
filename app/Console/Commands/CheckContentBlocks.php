<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ContentBlock;
use App\Models\CourseSection;

class CheckContentBlocks extends Command
{
    protected $signature = 'debug:check-blocks';
    protected $description = 'Check content blocks in the database';

    public function handle()
    {
        $this->info('Checking content blocks...');
        
        $sections = CourseSection::with('contentBlocks')->get();
        
        foreach ($sections as $section) {
            $this->info("\nSection {$section->id}: {$section->title}");
            $this->info("Content blocks count: " . $section->contentBlocks->count());
            
            foreach ($section->contentBlocks as $block) {
                $this->info("\nBlock ID: {$block->id}");
                $this->info("Type: {$block->type}");
                
                switch($block->type) {
                    case 'text':
                        $this->info("Text Content: " . $block->text_content);
                        break;
                    case 'video':
                        $this->info("Video URL: " . $block->video_url);
                        $this->info("Video Title: " . $block->video_title);
                        break;
                    case 'image':
                        $this->info("Image Path: " . $block->image_path);
                        break;
                    case 'quiz':
                        $this->info("Quiz Data: " . json_encode($block->quiz_data, JSON_PRETTY_PRINT));
                        break;
                }
                
                $this->info("Formatted Content: " . json_encode($block->formatted_content, JSON_PRETTY_PRINT));
            }
        }
    }
}
