<nav class="space-y-3">
    @foreach($sections as $section)
        <button
            onclick="loadSection({{ $section->id }})"
            class="w-full flex items-center p-3 rounded-xl transition-all duration-200
                {{ $section->id == $progress->current_section_id ? 'bg-orange-50 border-orange-200' : 'hover:bg-gray-50' }}
                {{ in_array($section->id, $progress->completed_sections ?? []) ? 'border-green-200' : 'border-gray-200' }}
                border"
        >
            <div class="flex-shrink-0 mr-3">
                @if(in_array($section->id, $progress->completed_sections ?? []))
                    <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                @elseif($section->id == $progress->current_section_id)
                    <div class="w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center">
                        <div class="w-2 h-2 bg-white rounded-full"></div>
                    </div>
                @else
                    <div class="w-6 h-6 border-2 border-gray-300 rounded-full flex items-center justify-center">
                        <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                    </div>
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium {{ $section->id == $progress->current_section_id ? 'text-orange-600' : 'text-gray-900' }} truncate">
                        {{ $section->title }}
                    </span>
                    @if(in_array($section->id, $progress->completed_sections ?? []))
                        <span class="inline-flex items-center rounded-lg bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                            Completed
                        </span>
                    @endif
                </div>
                @if($section->id == $progress->current_section_id)
                    <p class="mt-0.5 text-xs text-orange-500">Current section</p>
                @endif
            </div>
        </button>
    @endforeach
</nav>
