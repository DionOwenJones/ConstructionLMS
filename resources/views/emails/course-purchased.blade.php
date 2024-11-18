<x-mail::message>
# Course Purchase Confirmation

Dear {{ $business->name }},

Thank you for purchasing access to the following course:

<x-mail::panel>
# {{ $course->title }}

{{ $course->description }}

**Seats Purchased:** {{ $seats }}
</x-mail::panel>

You can now allocate this course to your employees. Click the button below to manage course allocations:

<x-mail::button :url="route('business.courses.purchases')">
Manage Allocations
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
