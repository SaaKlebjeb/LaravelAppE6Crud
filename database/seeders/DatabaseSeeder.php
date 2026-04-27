<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Student::factory(80)->create();

        Student::factory()->create([
            'student_code' => 'STD-000001',
            'first_name' => 'System',
            'last_name' => 'Demo',
            'email' => 'demo.student@example.com',
            'major' => 'Computer Science',
            'status' => 'active',
            'is_active' => true,
            'enrollment_date' => now()->subYear()->format('Y-m-d'),
        ]);
    }
}
