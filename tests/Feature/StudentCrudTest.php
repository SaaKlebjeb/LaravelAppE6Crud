<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_students_dashboard_is_accessible(): void
    {
        Student::factory()->count(3)->create();

        $response = $this->get(route('students.index'));

        $response->assertOk();
        $response->assertSee('Student Management Dashboard');
    }

    public function test_can_create_student(): void
    {
        $response = $this->post(route('students.store'), [
            'student_code' => 'STD-123456',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'major' => 'Computer Science',
            'status' => 'active',
            'enrollment_date' => now()->toDateString(),
            'is_active' => '1',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('students', [
            'student_code' => 'STD-123456',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    }

    public function test_can_update_student(): void
    {
        $student = Student::factory()->create();

        $response = $this->put(route('students.update', $student), [
            'student_code' => $student->student_code,
            'first_name' => 'Updated',
            'last_name' => $student->last_name,
            'email' => $student->email,
            'major' => 'Information Technology',
            'status' => 'suspended',
            'enrollment_date' => now()->toDateString(),
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'first_name' => 'Updated',
            'status' => 'suspended',
            'major' => 'Information Technology',
        ]);
    }

    public function test_can_soft_delete_and_restore_student(): void
    {
        $student = Student::factory()->create();

        $this->delete(route('students.destroy', $student))->assertRedirect();
        $this->assertSoftDeleted('students', ['id' => $student->id]);

        $this->patch(route('students.restore', $student->id))->assertRedirect();
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'deleted_at' => null,
        ]);
    }
}
