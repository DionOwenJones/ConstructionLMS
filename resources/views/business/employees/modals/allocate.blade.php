@foreach($employees as $employee)
<div class="modal fade" id="allocateModal-{{ $employee->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Allocate Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('business.employees.allocate-course', $employee) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Select Course</label>
                        <select name="course_id" class="form-select" required>
                            <option value="">Choose a course...</option>
                            @foreach($availableCourses as $course)
                                <option value="{{ $course->id }}">
                                    {{ $course->name }} ({{ $course->available_seats }} seats)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Allocate Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
