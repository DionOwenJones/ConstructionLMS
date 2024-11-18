@props(['videoUrl'])

@php
    // Extract YouTube video ID from URL
    $videoId = null;
    $url = parse_url($videoUrl);
    
    if (isset($url['query'])) {
        parse_str($url['query'], $query);
        $videoId = $query['v'] ?? null;
    } elseif (isset($url['path'])) {
        // Handle youtu.be format
        $videoId = trim($url['path'], '/');
    }
@endphp

@if($videoId)
    <div class="w-full max-w-5xl mx-auto">
        <div class="relative" style="padding-bottom: 56.25%;">
            <iframe 
                src="https://www.youtube.com/embed/{{ $videoId }}"
                class="absolute top-0 left-0 w-full h-full rounded-lg shadow-lg"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
            </iframe>
        </div>
    </div>
@else
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">Invalid video URL!</strong>
        <span class="block sm:inline">Please provide a valid YouTube URL.</span>
    </div>
@endif
