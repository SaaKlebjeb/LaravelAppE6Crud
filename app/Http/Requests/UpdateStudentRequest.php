<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('student')?->id;

        return [
            'student_code' => ['required', 'string', 'max:30', 'alpha_dash', Rule::unique('students', 'student_code')->ignore($studentId)],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('students', 'email')->ignore($studentId)],
            'phone' => ['nullable', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(Student::GENDERS)],
            'major' => ['required', 'string', 'max:120'],
            'status' => ['required', Rule::in(Student::STATUSES)],
            'enrollment_date' => ['required', 'date'],
            'gpa' => ['nullable', 'numeric', 'between:0,4.00'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('student_code')) {
            $this->merge([
                'student_code' => strtoupper(trim((string) $this->student_code)),
            ]);
        }
    }
}
