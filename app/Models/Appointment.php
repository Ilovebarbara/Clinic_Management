<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'attending_staff_id',
        'appointment_date',
        'appointment_type',
        'status',
        'notes',
        'queue_number',
        'window_number',
        'reason',
        'symptoms',
        'preferred_time',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Appointment types
     */
    const APPOINTMENT_TYPES = [
        'consultation' => 'General Consultation',
        'dental_consultation' => 'Dental Consultation',
        'follow_up' => 'Follow-up',
        'check_up' => 'Check-up',
        'emergency' => 'Emergency',
        'vaccination' => 'Vaccination',
        'physical_exam' => 'Physical Examination',
        'laboratory' => 'Laboratory Tests',
        'specialist_consultation' => 'Specialist Consultation',
    ];

    /**
     * Appointment statuses
     */
    const STATUSES = [
        'scheduled' => 'Scheduled',
        'confirmed' => 'Confirmed',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'no_show' => 'No Show',
        'rescheduled' => 'Rescheduled',
    ];

    /**
     * Check if appointment is upcoming
     */
    public function getIsUpcomingAttribute()
    {
        return $this->appointment_date->isFuture() && 
               in_array($this->status, ['scheduled', 'confirmed']);
    }

    /**
     * Check if appointment is today
     */
    public function getIsTodayAttribute()
    {
        return $this->appointment_date->isToday();
    }

    /**
     * Get formatted appointment date
     */
    public function getFormattedDateAttribute()
    {
        return $this->appointment_date->format('M d, Y');
    }

    /**
     * Get formatted appointment time
     */
    public function getFormattedTimeAttribute()
    {
        return $this->appointment_date->format('h:i A');
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'scheduled' => 'primary',
            'confirmed' => 'info',
            'in_progress' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            'no_show' => 'secondary',
            'rescheduled' => 'dark',
            default => 'secondary'
        };
    }

    /**
     * Relationships
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function attendingStaff()
    {
        return $this->belongsTo(User::class, 'attending_staff_id');
    }

    /**
     * Scopes
     */
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now())
                    ->whereIn('status', ['scheduled', 'confirmed']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', today());
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('appointment_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('appointment_date', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }
}
