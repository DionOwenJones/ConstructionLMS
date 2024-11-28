<div id="section-{{ $section->id }}" class="section-content">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-2xl font-bold text-gray-900">{{ $section->title }}</h2>
    </div>
    <div class="p-6">
        @php
            $sectionContent = json_decode($section->content, true);
            \Illuminate\Support\Facades\Log::info('Section content:', ['content' => $sectionContent]);
        @endphp

        @if(isset($sectionContent['type']) && isset($sectionContent['data']))
            @switch($sectionContent['type'])
                @case('video')
                    @if(isset($sectionContent['data']['video_url']))
                        <div class="py-4">
                            <x-video-player 
                                :videoUrl="$sectionContent['data']['video_url']"
                                size="large"
                            />
                        </div>
                    @endif
                    @break

                @case('text')
                    @if(isset($sectionContent['data']['text']))
                        <div class="prose max-w-none">
                            {!! $sectionContent['data']['text'] !!}
                        </div>
                    @endif
                    @break

                @case('quiz')
                    @if(isset($sectionContent['data']['questions']))
                        <div id="quiz-section-{{ $section->id }}" class="py-4 space-y-6">
                            @foreach($sectionContent['data']['questions'] as $index => $question)
                                <div class="quiz-question bg-white rounded-lg shadow-sm p-6 space-y-4">
                                    <h3 class="text-lg font-medium text-gray-900">Question {{ $index + 1 }}</h3>
                                    <p class="text-gray-700">{{ $question['text'] }}</p>
                                    
                                    <div class="space-y-3">
                                        @foreach($question['options'] as $optionIndex => $option)
                                            <label class="flex items-center space-x-3">
                                                <input type="radio" 
                                                       name="quiz_{{ $section->id }}_question_{{ $index }}" 
                                                       value="{{ $optionIndex }}"
                                                       class="text-orange-600 focus:ring-orange-500 h-4 w-4 border-gray-300">
                                                <span class="text-gray-700">{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="mt-6">
                                <button type="button" 
                                        onclick="submitQuiz({{ $section->id }})"
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                    Submit Quiz
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="text-gray-500 italic">
                            No questions available for this quiz.
                        </div>
                    @endif
                    @break

                @default
                    <div class="text-gray-500 italic">
                        Debug info:
                        <pre class="mt-2 p-4 bg-gray-100 rounded-lg overflow-auto">
                            {{ print_r($sectionContent, true) }}
                        </pre>
                    </div>
            @endswitch
        @else
            <div class="text-gray-500 italic">
                Debug info:
                <pre class="mt-2 p-4 bg-gray-100 rounded-lg overflow-auto">
                    {{ print_r($sectionContent, true) }}
                </pre>
            </div>
        @endif
    </div>

    <!-- Navigation Buttons -->
    <div class="p-6 bg-gray-50 border-t border-gray-200">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                @if($section->id !== $course->sections->first()->id)
                    <form action="{{ route('courses.previous-section', ['id' => $course->id, 'sectionId' => $section->id]) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            Previous
                        </button>
                    </form>
                @endif
            </div>
            <div class="flex items-center space-x-4">
                @if($section->id !== $course->sections->last()->id)
                    <form action="{{ route('courses.next-section', ['id' => $course->id, 'sectionId' => $section->id]) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            Next
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
