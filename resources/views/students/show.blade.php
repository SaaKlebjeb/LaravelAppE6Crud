@extends('layouts.app')

@section('title', 'Student Detail')

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold">{{ $student->full_name }}</h2>
                <p class="text-sm text-slate-500">Code: {{ $student->student_code }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('students.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back</a>
                @if (!$student->trashed())
                    <a href="{{ route('students.edit', $student) }}" class="rounded-lg border border-blue-300 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50">Edit</a>
                    <form method="POST" action="{{ route('students.destroy', $student) }}" onsubmit="return confirm('Move this student to recycle bin?');">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-lg border border-red-300 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Delete</button>
                    </form>
                @endif
            </div>
        </div>

        <dl class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-slate-200 p-4"><dt class="text-xs uppercase text-slate-500">Email</dt><dd class="text-sm">{{ $student->email ?: '-' }}</dd></div>
            <div class="rounded-lg border border-slate-200 p-4"><dt class="text-xs uppercase text-slate-500">Phone</dt><dd class="text-sm">{{ $student->phone ?: '-' }}</dd></div>
            <div class="rounded-lg border border-slate-200 p-4"><dt class="text-xs uppercase text-slate-500">Major</dt><dd class="text-sm">{{ $student->major }}</dd></div>
            <div class="rounded-lg border border-slate-200 p-4"><dt class="text-xs uppercase text-slate-500">Status</dt><dd class="text-sm capitalize">{{ $student->status }}</dd></div>
            <div class="rounded-lg border border-slate-200 p-4"><dt class="text-xs uppercase text-slate-500">Gender</dt><dd class="text-sm capitalize">{{ $student->gender ?: '-' }}</dd></div>
            <div class="rounded-lg border border-slate-200 p-4"><dt class="text-xs uppercase text-slate-500">Date of Birth</dt><dd class="text-sm">{{ $student->date_of_birth?->format('Y-m-d') ?: '-' }}</dd></div>
            <div class="rounded-lg border border-slate-200 p-4"><dt class="text-xs uppercase text-slate-500">Enrollment Date</dt><dd class="text-sm">{{ $student->enrollment_date?->format('Y-m-d') }}</dd></div>
            <div class="rounded-lg border border-slate-200 p-4"><dt class="text-xs uppercase text-slate-500">GPA</dt><dd class="text-sm">{{ $student->gpa ?: '-' }}</dd></div>
            <div class="rounded-lg border border-slate-200 p-4 md:col-span-2"><dt class="text-xs uppercase text-slate-500">Address</dt><dd class="text-sm">{{ $student->address ?: '-' }}</dd></div>
            <div class="rounded-lg border border-slate-200 p-4 md:col-span-2"><dt class="text-xs uppercase text-slate-500">Notes</dt><dd class="text-sm">{{ $student->notes ?: '-' }}</dd></div>
        </dl>
    </section>
@endsection
