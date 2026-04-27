<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const STATUSES = ['active', 'suspended', 'graduated', 'dropped'];
    public const GENDERS = ['male', 'female', 'other'];

    protected $fillable = [
        'student_code',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'major',
        'status',
        'enrollment_date',
        'gpa',
        'address',
        'notes',
        'is_active',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'enrollment_date' => 'date',
            'is_active' => 'boolean',
            'last_activity_at' => 'datetime',
            'gpa' => 'decimal:2',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $query
            ->when($filters['q'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $builder) use ($search) {
                    $builder
                        ->where('student_code', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('major', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['major'] ?? null, fn (Builder $query, string $major) => $query->where('major', 'like', "%{$major}%"))
            ->when($filters['gender'] ?? null, fn (Builder $query, string $gender) => $query->where('gender', $gender))
            ->when(isset($filters['active']) && $filters['active'] !== '', fn (Builder $query) => $query->where('is_active', (bool) $filters['active']))
            ->when($filters['enrolled_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('enrollment_date', '>=', $date))
            ->when($filters['enrolled_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('enrollment_date', '<=', $date));

        return $query;
    }

    public function scopeSorted(Builder $query, ?string $sortBy, ?string $direction): Builder
    {
        $allowedSorts = ['student_code', 'first_name', 'last_name', 'major', 'status', 'enrollment_date', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSorts, true) ? $sortBy : 'created_at';
        $direction = $direction === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sortBy, $direction);
    }
}
