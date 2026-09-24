<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'username', 'role', 'password', 'must_change_password', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function teacherProfile()
    {
        return $this->hasOne(TeacherProfile::class);
    }

    public function teachingAssignments()
    {
        return $this->hasMany(TeachingAssignment::class, 'teacher_user_id');
    }

    public function parentProfile()
    {
        return $this->hasOne(ParentProfile::class);
    }

    public function children()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'parent_user_id', 'student_user_id')
            ->withPivot('relationship')
            ->withTimestamps();
    }

    public function pointSummary()
    {
        return $this->hasOne(StudentPointSummary::class, 'student_user_id');
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class, 'student_user_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(string ...$roles): bool
    {
        if (in_array($this->role, $roles, true)) {
            return true;
        }

        if ($this->relationLoaded('roles')) {
            return $this->roles->whereIn('name', $roles)->isNotEmpty();
        }

        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->hasRole(...$roles);
    }

    public function canDo(string $permission): bool
    {
        if ($this->hasRole('superadmin')) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', fn ($query) => $query->where('name', $permission))
            ->exists()
            || Role::query()
                ->where('name', $this->role)
                ->whereHas('permissions', fn ($query) => $query->where('name', $permission))
                ->exists();
    }

    public function bills()
    {
        return $this->hasMany(StudentBill::class, 'student_user_id');
    }

    public function classHistories()
    {
        return $this->hasMany(StudentClassHistory::class, 'student_user_id');
    }

    public function isRole(string $role): bool
    {
        return $this->hasRole($role);
    }
}
