<div class="modal fade" id="allocateModal-{{ $employee->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-bold">Allocate Course to {{ $employee->user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('business.employees.allocate-course', $employee) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div>
                        <label class="block text-sm font-medium mb-1" for="course">Select Course</label>
                        <select id="course"
                                class="form-select w-full"
                                name="course_id"
                                required>
                            <option value="">Choose a course...</option>
                            @foreach($availableCourses as $course)
                                <option value="{{ $course['id'] }}">
                                    {{ $course['name'] }} ({{ $course['available_seats'] }} seats available)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sm border-slate-200 hover:border-slate-300 text-slate-600" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-sm bg-indigo-500 hover:bg-indigo-600 text-white">Allocate Course</button>
                </div>
            </form>
        </div>
    </div>
</div>
