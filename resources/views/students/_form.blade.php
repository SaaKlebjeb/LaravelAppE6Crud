@php
    $isEdit = $student->exists;
@endphp

<div class="grid gap-6 md:grid-cols-2">
    <div>
        <label for="student_code" class="mb-1 block text-sm font-medium">Student Code *</label>
        <input id="student_code" name="student_code" value="{{ old('student_code', $student->student_code) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2" />
    </div>

    <div>
        <label for="major" class="mb-1 block text-sm font-medium">Major *</label>
        <input id="major" name="major" value="{{ old('major', $student->major) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2" />
    </div>

    <div>
        <label for="first_name" class="mb-1 block text-sm font-medium">First Name *</label>
        <input id="first_name" name="first_name" value="{{ old('first_name', $student->first_name) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2" />
    </div>

    <div>
        <label for="last_name" class="mb-1 block text-sm font-medium">Last Name *</label>
        <input id="last_name" name="last_name" value="{{ old('last_name', $student->last_name) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2" />
    </div>

    <div>
        <label for="email" class="mb-1 block text-sm font-medium">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $student->email) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
    </div>

    <div>
        <label for="phone" class="mb-1 block text-sm font-medium">Phone</label>
        <input id="phone" name="phone" value="{{ old('phone', $student->phone) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
    </div>

    <div>
        <label for="date_of_birth" class="mb-1 block text-sm font-medium">Date of Birth</label>
        <input id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth', optional($student->date_of_birth)->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
    </div>

    <div>
        <label for="enrollment_date" class="mb-1 block text-sm font-medium">Enrollment Date *</label>
        <input id="enrollment_date" name="enrollment_date" type="date" value="{{ old('enrollment_date', optional($student->enrollment_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2" />
    </div>

    <div>
        <label for="gender" class="mb-1 block text-sm font-medium">Gender</label>
        <select id="gender" name="gender" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            <option value="">Select gender</option>
            @foreach ($genders as $gender)
                <option value="{{ $gender }}" @selected(old('gender', $student->gender) === $gender)>{{ ucfirst($gender) }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="status" class="mb-1 block text-sm font-medium">Status *</label>
        <select id="status" name="status" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $student->status ?: 'active') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="gpa" class="mb-1 block text-sm font-medium">GPA</label>
        <input id="gpa" name="gpa" type="number" min="0" max="4" step="0.01" value="{{ old('gpa', $student->gpa) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
    </div>

    <div class="flex items-center gap-2 pt-7">
        <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $isEdit ? $student->is_active : true)) class="h-4 w-4 rounded border-slate-300" />
        <label for="is_active" class="text-sm font-medium">Mark as active</label>
    </div>

    <div class="md:col-span-2">
        <label for="address" class="mb-1 block text-sm font-medium">Address</label>
        <textarea id="address" name="address" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('address', $student->address) }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label for="notes" class="mb-1 block text-sm font-medium">Notes</label>
        <textarea id="notes" name="notes" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('notes', $student->notes) }}</textarea>
    </div>
</div>

<div class="mt-6 flex flex-wrap items-center gap-3">
    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
        {{ $isEdit ? 'Update Student' : 'Create Student' }}
    </button>
    <a href="{{ route('students.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
        Cancel
    </a>
</div>
