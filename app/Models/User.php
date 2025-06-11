<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'role',
        'phone',
        'employee_id',
        'profile_picture',
        'is_active',
        'created_by_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Define user roles
     */
    const ROLES = [
        'super_admin' => 'Super Admin',
        'staff' => 'Staff',
        'nurse' => 'Nurse',
        'dentist' => 'Dentist',
        'physician' => 'Physician',
    ];

    /**
     * Get full name
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if user has specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is staff member
     */
    public function isStaff()
    {
        return in_array($this->role, ['super_admin', 'staff', 'nurse', 'dentist', 'physician']);
    }

    /**
     * Relationships
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function createdUsers()
    {
        return $this->hasMany(User::class, 'created_by_id');
    }

    public function patientsCreated()
    {
        return $this->hasMany(Patient::class, 'created_by_id');
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'attending_staff_id');
    }

    public function appointmentsAttended()
    {
        return $this->hasMany(Appointment::class, 'attending_staff_id');
    }

    public function doctorProfile()
    {
        return $this->hasOne(Doctor::class);
    }

    /**
     * Get the color class for user role
     */
    public function getRoleColorAttribute()
    {
        return match($this->role) {
            'super_admin' => 'danger',
            'admin' => 'warning',
            'doctor' => 'primary',
            'nurse' => 'info',
            'staff' => 'secondary',
            default => 'secondary'
        };
    }
}
