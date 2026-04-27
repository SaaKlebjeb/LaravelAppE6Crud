@extends('layouts.app')

@section('title', 'Students Dashboard')

@section('content')
    <section class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Total Students</p>
            <p class="mt-1 text-2xl font-bold">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Active Students</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600">{{ $stats['active'] }}</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Graduated</p>
            <p class="mt-1 text-2xl font-bold text-blue-600">{{ $stats['graduated'] }}</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">In Recycle Bin</p>
            <p class="mt-1 text-2xl font-bold text-amber-600">{{ $stats['trashed'] }}</p>
        </div>
    </section>

    <section class="mb-6 rounded-2xl bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('students.index') }}" class="grid gap-3 md:grid-cols-3 lg:grid-cols-5">
            <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search name, code, major..." class="rounded-lg border border-slate-300 px-3 py-2" />

            <select name="status" class="rounded-lg border border-slate-300 px-3 py-2">
                <option value="">All status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>

            <select name="gender" class="rounded-lg border border-slate-300 px-3 py-2">
                <option value="">All gender</option>
                @foreach ($genders as $gender)
                    <option value="{{ $gender }}" @selected(($filters['gender'] ?? '') === $gender)>{{ ucfirst($gender) }}</option>
                @endforeach
            </select>

            <select name="active" class="rounded-lg border border-slate-300 px-3 py-2">
                <option value="">All activity</option>
                <option value="1" @selected(($filters['active'] ?? '') === '1')>Active only</option>
                <option value="0" @selected(($filters['active'] ?? '') === '0')>Inactive only</option>
            </select>

            <select name="trashed" class="rounded-lg border border-slate-300 px-3 py-2">
                <option value="">Without trashed</option>
                <option value="with" @selected(($filters['trashed'] ?? '') === 'with')>Include trashed</option>
                <option value="only" @selected(($filters['trashed'] ?? '') === 'only')>Only trashed</option>
            </select>

            <input name="major" value="{{ $filters['major'] ?? '' }}" placeholder="Filter by major" class="rounded-lg border border-slate-300 px-3 py-2" />
            <input name="enrolled_from" type="date" value="{{ $filters['enrolled_from'] ?? '' }}" class="rounded-lg border border-slate-300 px-3 py-2" />
            <input name="enrolled_to" type="date" value="{{ $filters['enrolled_to'] ?? '' }}" class="rounded-lg border border-slate-300 px-3 py-2" />

            <select name="sort_by" class="rounded-lg border border-slate-300 px-3 py-2">
                <option value="created_at" @selected(($filters['sort_by'] ?? '') === 'created_at')>Sort by Created At</option>
                <option value="student_code" @selected(($filters['sort_by'] ?? '') === 'student_code')>Sort by Student Code</option>
                <option value="first_name" @selected(($filters['sort_by'] ?? '') === 'first_name')>Sort by First Name</option>
                <option value="last_name" @selected(($filters['sort_by'] ?? '') === 'last_name')>Sort by Last Name</option>
                <option value="enrollment_date" @selected(($filters['sort_by'] ?? '') === 'enrollment_date')>Sort by Enrollment Date</option>
            </select>

            <select name="direction" class="rounded-lg border border-slate-300 px-3 py-2">
                <option value="desc" @selected(($filters['direction'] ?? '') === 'desc')>Desc</option>
                <option value="asc" @selected(($filters['direction'] ?? '') === 'asc')>Asc</option>
            </select>

            <div class="md:col-span-3 lg:col-span-5 flex flex-wrap gap-2">
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Apply Filters</button>
                <a href="{{ route('students.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
                <a href="{{ route('students.export.csv', request()->query()) }}" class="rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">Export CSV</a>
            </div>
        </form>
    </section>

    <section class="rounded-2xl bg-white p-4 shadow-sm">
        <form method="POST" action="{{ route('students.bulk-action') }}" onsubmit="return confirm('Execute this bulk action?');">
            @csrf

            <div class="mb-3 flex flex-wrap items-center gap-2">
                <select name="action" required class="rounded-lg border border-slate-300 px-3 py-2">
                    <option value="">Bulk action...</option>
                    <option value="delete">Move to recycle bin</option>
                    <option value="restore">Restore</option>
                    <option value="activate">Activate</option>
                    <option value="deactivate">Deactivate</option>
                    <option value="graduate">Mark as graduated</option>
                    <option value="force_delete">Permanently delete (trashed only)</option>
                </select>
                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Run</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left"><input type="checkbox" onclick="document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = this.checked)"></th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Code</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Student</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Major</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Active</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Enrolled</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold uppercase text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($students as $student)
                            <tr class="{{ $student->trashed() ? 'bg-amber-50/40' : '' }}">
                                <td class="px-3 py-2">
                                    <input type="checkbox" class="student-checkbox" name="student_ids[]" value="{{ $student->id }}">
                                </td>
                                <td class="px-3 py-2 text-sm font-medium">{{ $student->student_code }}</td>
                                <td class="px-3 py-2 text-sm">
                                    <div>{{ $student->full_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $student->email }}</div>
                                </td>
                                <td class="px-3 py-2 text-sm">{{ $student->major }}</td>
                                <td class="px-3 py-2 text-sm capitalize">{{ $student->status }}</td>
                                <td class="px-3 py-2 text-sm">{{ $student->is_active ? 'Yes' : 'No' }}</td>
                                <td class="px-3 py-2 text-sm">{{ $student->enrollment_date?->format('Y-m-d') }}</td>
                                <td class="px-3 py-2 text-right text-sm">
                                    <a href="{{ route('students.show', $student->id) }}" class="rounded border border-slate-300 px-2 py-1 hover:bg-slate-50">View</a>
                                    @if (!$student->trashed())
                                        <a href="{{ route('students.edit', $student->id) }}" class="rounded border border-blue-300 px-2 py-1 text-blue-700 hover:bg-blue-50">Edit</a>
                                        <form method="POST" action="{{ route('students.destroy', $student->id) }}" class="inline" onsubmit="return confirm('Move this student to recycle bin?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded border border-red-300 px-2 py-1 text-red-700 hover:bg-red-50">Delete</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('students.restore', $student->id) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="rounded border border-emerald-300 px-2 py-1 text-emerald-700 hover:bg-emerald-50">Restore</button>
                                        </form>
                                        <form method="POST" action="{{ route('students.force-delete', $student->id) }}" class="inline" onsubmit="return confirm('Permanently delete this student?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded border border-red-500 px-2 py-1 text-red-700 hover:bg-red-50">Force Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-8 text-center text-sm text-slate-500">No students found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <div class="mt-4">
            {{ $students->links() }}
        </div>
    </section>
@endsection
