<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'attending_staff_id',
        'transaction_type',
        'date_time',
        'transaction_details',
        'height',
        'weight',
        'hr',
        'rr',
        'temperature',
        'bp_systolic',
        'bp_diastolic',
        'pain_scale',
        'other_symptoms',
        'assessment',
        'initial_diagnosis',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'height' => 'float',
        'weight' => 'float',
        'temperature' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Transaction types
     */
    const TRANSACTION_TYPES = [
        'consultation' => 'Medical Consultation',
        'dental_consultation' => 'Dental Consultation',
        'certificate' => 'Medical Certificate',
        'follow_up' => 'Follow-up',
        'emergency' => 'Emergency',
        'laboratory' => 'Laboratory',
        'pharmacy' => 'Pharmacy',
    ];

    /**
     * Diagnosis options
     */
    const DIAGNOSIS_OPTIONS = [
        'A2024',
        'ABDL DCFT (Abdominal Discomfort)',
        'Acute Asthma',
        'AGE (Acute Gastroenteritis)',
        'Allergy',
        'Animal Bite',
        'APE (Annual Physical Examination)',
        'ATP (Acute Tonsilo-Pharyngitis)',
        'Burn',
        'Cardiac Related Disorder',
        'Dental Disorder',
        'Dysmenorrhea',
        'Ear Disorder',
        'Epistaxis',
        'Eye Disorder',
        'GERD (Gastro-Esophageal Reflux Disease)',
        'SRI (Sports Related Injury)',
        'Headache',
        'Hypertension',
        'Diabetes',
        'Common Cold',
        'Fever',
        'Migraine',
        'Back Pain',
        'Muscle Strain',
        'Joint Pain',
        'Skin Condition',
        'Respiratory Infection',
        'Urinary Tract Infection',
        'Gastritis',
        'Anxiety',
        'Depression',
        'Insomnia',
        'Other',
    ];

    /**
     * Get BMI
     */
    public function getBmiAttribute()
    {
        if ($this->height && $this->weight) {
            $heightInMeters = $this->height / 100;
            return round($this->weight / ($heightInMeters * $heightInMeters), 2);
        }
        return null;
    }

    /**
     * Get BMI category
     */
    public function getBmiCategoryAttribute()
    {
        $bmi = $this->bmi;
        if (!$bmi) return null;

        if ($bmi < 18.5) return 'Underweight';
        if ($bmi < 25) return 'Normal';
        if ($bmi < 30) return 'Overweight';
        return 'Obese';
    }

    /**
     * Get blood pressure category
     */
    public function getBloodPressureCategoryAttribute()
    {
        if (!$this->bp_systolic || !$this->bp_diastolic) return null;

        $systolic = $this->bp_systolic;
        $diastolic = $this->bp_diastolic;

        if ($systolic < 120 && $diastolic < 80) return 'Normal';
        if ($systolic < 130 && $diastolic < 80) return 'Elevated';
        if ($systolic < 140 || $diastolic < 90) return 'High Blood Pressure Stage 1';
        if ($systolic >= 140 || $diastolic >= 90) return 'High Blood Pressure Stage 2';
        if ($systolic > 180 || $diastolic > 120) return 'Hypertensive Crisis';

        return 'Unknown';
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
    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeByTransactionType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('date_time', '>=', now()->subDays($days));
    }
}
