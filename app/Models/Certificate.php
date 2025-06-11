<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'issued_by_id',
        'certificate_type',
        'certificate_number',
        'diagnosis',
        'recommendations',
        'date_from',
        'date_to',
        'purpose',
        'status',
        'issued_at',
        'valid_until',
        'remarks',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'issued_at' => 'datetime',
        'valid_until' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Certificate types
     */
    const CERTIFICATE_TYPES = [
        'absence' => 'Certificate of Absence',
        'employment' => 'Certificate for Employment',
        'ojt' => 'Certificate for OJT/Internship',
        'clearance' => 'Medical Clearance',
        'fitness' => 'Certificate of Fitness',
        'vaccination' => 'Vaccination Certificate',
        'laboratory' => 'Laboratory Results Certificate',
        'general' => 'General Medical Certificate',
    ];

    /**
     * Certificate statuses
     */
    const STATUSES = [
        'draft' => 'Draft',
        'issued' => 'Issued',
        'expired' => 'Expired',
        'revoked' => 'Revoked',
    ];

    /**
     * Get certificate type label
     */
    public function getCertificateTypeLabelAttribute()
    {
        return self::CERTIFICATE_TYPES[$this->certificate_type] ?? 'Unknown';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status] ?? 'Unknown';
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'secondary',
            'issued' => 'success',
            'expired' => 'warning',
            'revoked' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Check if certificate is valid
     */
    public function getIsValidAttribute()
    {
        return $this->status === 'issued' && 
               ($this->valid_until === null || $this->valid_until->isFuture());
    }

    /**
     * Check if certificate is expired
     */
    public function getIsExpiredAttribute()
    {
        return $this->valid_until && $this->valid_until->isPast();
    }

    /**
     * Get days until expiration
     */
    public function getDaysUntilExpirationAttribute()
    {
        if (!$this->valid_until) return null;
        
        $diff = now()->diffInDays($this->valid_until, false);
        return $diff > 0 ? $diff : 0;
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

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by_id');
    }

    /**
     * Scopes
     */
    public function scopeValid($query)
    {
        return $query->where('status', 'issued')
                    ->where(function($q) {
                        $q->whereNull('valid_until')
                          ->orWhere('valid_until', '>=', now());
                    });
    }

    public function scopeExpired($query)
    {
        return $query->where('valid_until', '<', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('certificate_type', $type);
    }

    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Generate unique certificate number
     */
    public static function generateCertificateNumber($type)
    {
        $prefix = match($type) {
            'absence' => 'ABS',
            'employment' => 'EMP',
            'ojt' => 'OJT',
            'clearance' => 'CLR',
            'fitness' => 'FIT',
            'vaccination' => 'VAC',
            'laboratory' => 'LAB',
            default => 'GEN'
        };

        $year = now()->format('Y');
        $month = now()->format('m');
        $count = self::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('certificate_type', $type)
                    ->count() + 1;

        return "{$prefix}-{$year}{$month}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
