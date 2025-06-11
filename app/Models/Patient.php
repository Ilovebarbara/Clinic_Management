<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Patient extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'patient_number',
        'email',
        'password',
        'first_name',
        'middle_name',
        'last_name',
        'age',
        'sex',
        'date_of_birth',
        'phone',
        'lot_number',
        'barangay_subdivision',
        'street',
        'city_municipality',
        'province',
        'campus',
        'college_office',
        'course_designation',
        'patient_type',
        'emergency_contact_name',
        'emergency_contact_relation',
        'emergency_contact_number',
        'blood_type',
        'allergies',
        'is_active',
        'created_by_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Patient types
     */
    const PATIENT_TYPES = [
        'student' => 'Student',
        'faculty' => 'Faculty',
        'non_academic' => 'Non-Academic Personnel',
    ];

    /**
     * Blood types
     */
    const BLOOD_TYPES = [
        'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'
    ];

    /**
     * Campus options
     */
    const CAMPUS_OPTIONS = [
        'Malolos Campus',
        'Meneses Campus',
        'Hagonoy Campus',
        'San Jose del Monte Campus',
    ];

    /**
     * College/Office options
     */
    const COLLEGE_OFFICE_OPTIONS = [
        'CICT', 'CON', 'COE', 'COED', 'CAS', 'CBA', 'CAFENR',
        'HR', 'Accounting', 'Registrar', 'Library', 'Maintenance',
    ];

    /**
     * Get full name
     */
    public function getFullNameAttribute()
    {
        if ($this->middle_name) {
            return "{$this->first_name} {$this->middle_name} {$this->last_name}";
        }
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get full address
     */
    public function getFullAddressAttribute()
    {
        $address = collect([
            $this->lot_number,
            $this->barangay_subdivision,
            $this->street,
            $this->city_municipality,
            $this->province
        ])->filter()->implode(', ');

        return $address;
    }

    /**
     * Get priority level for queue
     */
    public function getPriorityLevelAttribute()
    {
        return match($this->patient_type) {
            'faculty' => 1,
            'non_academic' => 2,
            'student' => 4,
            default => 4
        };
    }

    /**
     * Relationships
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function queueTickets()
    {
        return $this->hasMany(QueueTicket::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('patient_type', $type);
    }

    public function scopeByCampus($query, $campus)
    {
        return $query->where('campus', $campus);
    }
}
