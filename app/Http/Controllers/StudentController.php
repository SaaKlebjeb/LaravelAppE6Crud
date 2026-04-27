<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkStudentActionRequest;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentController extends Controller
{
    public function dashboard(Request $request)
    {
        $query = $this->buildIndexQuery($request);

        $students = $query->paginate(12)->withQueryString();

        $stats = [
            'total' => Student::count(),
            'active' => Student::where('status', 'active')->count(),
            'graduated' => Student::where('status', 'graduated')->count(),
            'trashed' => Student::onlyTrashed()->count(),
        ];

        return view('students.index', [
            'students' => $students,
            'stats' => $stats,
            'statuses' => Student::STATUSES,
            'genders' => Student::GENDERS,
            'filters' => $request->only([
                'q',
                'status',
                'major',
                'gender',
                'active',
                'enrolled_from',
                'enrolled_to',
                'trashed',
                'sort_by',
                'direction',
            ]),
        ]);
    }

    public function create()
    {
        return view('students.create', [
            'student' => new Student(),
            'statuses' => Student::STATUSES,
            'genders' => Student::GENDERS,
        ]);
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $payload['is_active'] = (bool) ($payload['is_active'] ?? true);
        $payload['last_activity_at'] = now();

        $student = Student::create($payload);

        return redirect()
            ->route('students.show', $student)
            ->with('success', 'Student created successfully.');
    }

    public function show(Student $student)
    {
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        return view('students.edit', [
            'student' => $student,
            'statuses' => Student::STATUSES,
            'genders' => Student::GENDERS,
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $payload = $request->validated();
        $payload['is_active'] = (bool) ($payload['is_active'] ?? false);

        $student->update($payload);

        return redirect()
            ->route('students.show', $student)
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('success', 'Student moved to recycle bin.');
    }

    public function restore(int $student): RedirectResponse
    {
        $studentModel = Student::onlyTrashed()->findOrFail($student);
        $studentModel->restore();

        return redirect()
            ->route('students.index', ['trashed' => 'only'])
            ->with('success', 'Student restored successfully.');
    }

    public function forceDelete(int $student): RedirectResponse
    {
        $studentModel = Student::onlyTrashed()->findOrFail($student);
        $studentModel->forceDelete();

        return redirect()
            ->route('students.index', ['trashed' => 'only'])
            ->with('success', 'Student permanently deleted.');
    }

    public function bulkAction(BulkStudentActionRequest $request): RedirectResponse
    {
        $ids = $request->validated()['student_ids'];
        $action = $request->validated()['action'];

        $query = Student::withTrashed()->whereIn('id', $ids);

        match ($action) {
            'delete' => $query->get()->each->delete(),
            'restore' => $query->onlyTrashed()->restore(),
            'activate' => $query->update(['is_active' => true]),
            'deactivate' => $query->update(['is_active' => false]),
            'graduate' => $query->update(['status' => 'graduated']),
            'force_delete' => $query->onlyTrashed()->forceDelete(),
        };

        return back()->with('success', 'Bulk action executed successfully.');
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $fileName = 'students_'.now()->format('Ymd_His').'.csv';
        $query = $this->buildIndexQuery($request);

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'wb');

            fputcsv($handle, [
                'ID',
                'Student Code',
                'Full Name',
                'Email',
                'Phone',
                'Major',
                'Status',
                'Gender',
                'GPA',
                'Enrollment Date',
                'Is Active',
                'Deleted At',
                'Created At',
            ]);

            $query->chunk(500, function ($students) use ($handle) {
                foreach ($students as $student) {
                    fputcsv($handle, [
                        $student->id,
                        $student->student_code,
                        $student->full_name,
                        $student->email,
                        $student->phone,
                        $student->major,
                        $student->status,
                        $student->gender,
                        $student->gpa,
                        $student->enrollment_date?->format('Y-m-d'),
                        $student->is_active ? 'Yes' : 'No',
                        $student->deleted_at?->format('Y-m-d H:i:s'),
                        $student->created_at?->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }

    private function buildIndexQuery(Request $request): Builder
    {
        $query = Student::query();

        if ($request->input('trashed') === 'with') {
            $query->withTrashed();
        }

        if ($request->input('trashed') === 'only') {
            $query->onlyTrashed();
        }

        return $query
            ->filter($request->only([
                'q',
                'status',
                'major',
                'gender',
                'active',
                'enrolled_from',
                'enrolled_to',
            ]))
            ->sorted($request->input('sort_by'), $request->input('direction'));
    }
}
