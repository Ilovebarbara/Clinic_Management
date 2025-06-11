<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'queue_number',
        'ticket_type',
        'transaction_type',
        'priority_level',
        'priority_type',
        'window_number',
        'status',
        'called_at',
        'completed_at',
        'walk_in_name',
        'walk_in_type',
        'estimated_wait_time',
        'service_notes',
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Ticket types
     */
    const TICKET_TYPES = [
        'appointment' => 'Appointment',
        'walk_in' => 'Walk-in',
    ];

    /**
     * Transaction types
     */
    const TRANSACTION_TYPES = [
        'consultation_medical' => 'Medical Consultation',
        'consultation_dental' => 'Dental Consultation',
        'certificate_absence' => 'Certificate of Absence',
        'certificate_employment' => 'Certificate for Employment',
        'certificate_ojt' => 'Certificate for OJT',
        'certificate_clearance' => 'Medical Clearance',
        'laboratory' => 'Laboratory Services',
        'pharmacy' => 'Pharmacy Services',
        'vaccination' => 'Vaccination',
        'emergency' => 'Emergency',
    ];

    /**
     * Priority levels
     */
    const PRIORITY_LEVELS = [
        1 => 'Faculty',
        2 => 'Personnel/Staff',
        3 => 'Senior Citizens',
        4 => 'Regular Students',
    ];

    /**
     * Priority types
     */
    const PRIORITY_TYPES = [
        'faculty' => 'Faculty',
        'personnel' => 'Personnel',
        'senior_citizen' => 'Senior Citizen',
        'regular' => 'Regular',
    ];

    /**
     * Queue statuses
     */
    const STATUSES = [
        'waiting' => 'Waiting',
        'serving' => 'Being Served',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'no_show' => 'No Show',
    ];

    /**
     * Walk-in types
     */
    const WALK_IN_TYPES = [
        'student' => 'Student',
        'faculty' => 'Faculty',
        'personnel' => 'Personnel',
        'visitor' => 'Visitor',
    ];

    /**
     * Get patient name (registered or walk-in)
     */
    public function getPatientNameAttribute()
    {
        if ($this->patient) {
            return $this->patient->full_name;
        }
        return $this->walk_in_name ?: 'Anonymous';
    }

    /**
     * Get priority color for UI
     */
    public function getPriorityColorAttribute()
    {
        return match($this->priority_level) {
            1 => 'success',      // Faculty - Green
            2 => 'warning',      // Personnel - Orange
            3 => 'info',         // Senior Citizens - Purple
            4 => 'secondary',    // Regular - Gray
            default => 'secondary'
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'waiting' => 'primary',
            'serving' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            'no_show' => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Calculate estimated wait time
     */
    public function getEstimatedWaitAttribute()
    {
        $position = self::where('status', 'waiting')
                       ->where('priority_level', '<=', $this->priority_level)
                       ->where('created_at', '<', $this->created_at)
                       ->count() + 1;

        $avgServiceTime = 15; // minutes per patient
        $estimatedMinutes = $position * $avgServiceTime;

        if ($estimatedMinutes < 60) {
            return "{$estimatedMinutes} minutes";
        } else {
            $hours = floor($estimatedMinutes / 60);
            $minutes = $estimatedMinutes % 60;
            return "{$hours}h {$minutes}m";
        }
    }

    /**
     * Get queue position
     */
    public function getPositionAttribute()
    {
        return self::where('status', 'waiting')
                  ->where(function($query) {
                      $query->where('priority_level', '<', $this->priority_level)
                            ->orWhere(function($q) {
                                $q->where('priority_level', $this->priority_level)
                                  ->where('created_at', '<', $this->created_at);
                            });
                  })
                  ->count() + 1;
    }

    /**
     * Check if ticket is active
     */
    public function getIsActiveAttribute()
    {
        return in_array($this->status, ['waiting', 'serving']);
    }

    /**
     * Relationships
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['waiting', 'serving']);
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    public function scopeServing($query)
    {
        return $query->where('status', 'serving');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority_level', 'asc')
                    ->orderBy('created_at', 'asc');
    }

    public function scopeByWindow($query, $windowNumber)
    {
        return $query->where('window_number', $windowNumber);
    }

    public function scopeByTransactionType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Generate unique queue number
     */
    public static function generateQueueNumber($transactionType = null)
    {
        $today = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        
        if ($transactionType) {
            $prefix = match($transactionType) {
                'consultation_medical' => 'MC',
                'consultation_dental' => 'DC',
                'certificate_absence' => 'CA',
                'certificate_employment' => 'CE',
                'certificate_ojt' => 'CO',
                'laboratory' => 'LAB',
                'pharmacy' => 'PH',
                default => 'GEN'
            };
            return "{$prefix}-{$today}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
        }
        
        return "{$today}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
