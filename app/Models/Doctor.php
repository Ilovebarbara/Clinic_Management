<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'specialization',
        'email',
        'phone',
        'license_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Medical specializations
     */
    const SPECIALIZATIONS = [
        'General Medicine',
        'Internal Medicine',
        'Pediatrics',
        'Obstetrics and Gynecology',
        'Surgery',
        'Orthopedics',
        'Dermatology',
        'Ophthalmology',
        'ENT (Ear, Nose, Throat)',
        'Psychiatry',
        'Neurology',
        'Cardiology',
        'Pulmonology',
        'Gastroenterology',
        'Nephrology',
        'Endocrinology',
        'Rheumatology',
        'Oncology',
        'Radiology',
        'Pathology',
        'Anesthesiology',
        'Family Medicine',
        'Emergency Medicine',
        'Sports Medicine',
        'Occupational Medicine',
        'Dentistry',
        'Oral Surgery',
        'Orthodontics',
        'Periodontics',
        'Endodontics',
        'Prosthodontics',
        'Oral Pathology',
    ];

    /**
     * Get full name
     */
    public function getFullNameAttribute()
    {
        return "Dr. {$this->first_name} {$this->last_name}";
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySpecialization($query, $specialization)
    {
        return $query->where('specialization', $specialization);
    }
}
