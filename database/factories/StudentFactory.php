<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'student_code' => 'STD-'.str_pad((string) fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'date_of_birth' => fake()->dateTimeBetween('-28 years', '-16 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(Student::GENDERS),
            'major' => fake()->randomElement([
                'Computer Science',
                'Information Technology',
                'Business Administration',
                'Accounting',
                'Marketing',
                'Electrical Engineering',
            ]),
            'status' => fake()->randomElement(Student::STATUSES),
            'enrollment_date' => fake()->dateTimeBetween('-4 years', 'now')->format('Y-m-d'),
            'gpa' => fake()->randomFloat(2, 2.0, 4.0),
            'address' => fake()->address(),
            'notes' => fake()->boolean(25) ? fake()->sentence() : null,
            'is_active' => fake()->boolean(85),
            'last_activity_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
