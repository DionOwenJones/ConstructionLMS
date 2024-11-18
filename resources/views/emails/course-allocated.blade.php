<x-mail::message>
# New Course Allocated

Hello {{ $user->name }},

You have been allocated a new course by {{ $businessName }}:

<x-mail::panel>
# {{ $course->title }}

{{ $course->description }}
</x-mail::panel>

<x-mail::button :url="route('courses.show', $course)">
Start Learning
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
