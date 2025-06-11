<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of medical records
     */
    public function index(Request $request)
    {
        $query = MedicalRecord::with(['patient', 'doctor', 'appointment']);

        // Filter by patient
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by doctor
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('consultation_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('consultation_date', '<=', $request->date_to);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('chief_complaint', 'like', "%{$search}%")
                  ->orWhere('diagnosis', 'like', "%{$search}%")
                  ->orWhere('treatment', 'like', "%{$search}%")
                  ->orWhereHas('patient', function ($patientQuery) use ($search) {
                      $patientQuery->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%")
                                  ->orWhere('patient_number', 'like', "%{$search}%");
                  });
            });
        }

        $records = $query->orderBy('consultation_date', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $records
        ]);
    }

    /**
     * Store a newly created medical record
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_id' => 'sometimes|exists:appointments,id',
            'consultation_date' => 'required|date',
            'chief_complaint' => 'required|string|max:1000',
            'history_present_illness' => 'sometimes|string|max:2000',
            'past_medical_history' => 'sometimes|string|max:1000',
            'family_history' => 'sometimes|string|max:1000',
            'social_history' => 'sometimes|string|max:1000',
            'allergies' => 'sometimes|string|max:500',
            'medications' => 'sometimes|string|max:1000',
            
            // Vital signs
            'temperature' => 'sometimes|numeric|between:30,45',
            'blood_pressure_systolic' => 'sometimes|integer|between:60,250',
            'blood_pressure_diastolic' => 'sometimes|integer|between:40,150',
            'heart_rate' => 'sometimes|integer|between:30,200',
            'respiratory_rate' => 'sometimes|integer|between:8,40',
            'oxygen_saturation' => 'sometimes|integer|between:70,100',
            'height' => 'sometimes|numeric|between:50,250',
            'weight' => 'sometimes|numeric|between:2,300',
            
            // Physical examination
            'physical_examination' => 'sometimes|string|max:2000',
            'assessment' => 'sometimes|string|max:1000',
            'diagnosis' => 'required|string|max:1000',
            'treatment' => 'required|string|max:2000',
            'prescription' => 'sometimes|string|max:1000',
            'follow_up_date' => 'sometimes|date|after:today',
            'notes' => 'sometimes|string|max:1000'
        ]);

        // If appointment_id is provided, update appointment status
        if ($request->filled('appointment_id')) {
            $appointment = Appointment::find($request->appointment_id);
            if ($appointment) {
                $appointment->update(['status' => 'completed']);
            }
        }

        $medicalRecord = MedicalRecord::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'appointment_id' => $request->appointment_id,
            'consultation_date' => $request->consultation_date,
            'chief_complaint' => $request->chief_complaint,
            'history_present_illness' => $request->history_present_illness,
            'past_medical_history' => $request->past_medical_history,
            'family_history' => $request->family_history,
            'social_history' => $request->social_history,
            'allergies' => $request->allergies,
            'medications' => $request->medications,
            
            // Vital signs
            'temperature' => $request->temperature,
            'blood_pressure_systolic' => $request->blood_pressure_systolic,
            'blood_pressure_diastolic' => $request->blood_pressure_diastolic,
            'heart_rate' => $request->heart_rate,
            'respiratory_rate' => $request->respiratory_rate,
            'oxygen_saturation' => $request->oxygen_saturation,
            'height' => $request->height,
            'weight' => $request->weight,
            
            // Physical examination and treatment
            'physical_examination' => $request->physical_examination,
            'assessment' => $request->assessment,
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'prescription' => $request->prescription,
            'follow_up_date' => $request->follow_up_date,
            'notes' => $request->notes,
            'created_by' => Auth::id()
        ]);

        $medicalRecord->load(['patient', 'doctor', 'appointment']);

        return response()->json([
            'success' => true,
            'message' => 'Medical record created successfully.',
            'data' => $medicalRecord
        ], 201);
    }

    /**
     * Display the specified medical record
     */
    public function show($id)
    {
        $medicalRecord = MedicalRecord::with(['patient', 'doctor', 'appointment'])->find($id);

        if (!$medicalRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Medical record not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $medicalRecord
        ]);
    }

    /**
     * Update the specified medical record
     */
    public function update(Request $request, $id)
    {
        $medicalRecord = MedicalRecord::find($id);

        if (!$medicalRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Medical record not found.'
            ], 404);
        }

        $request->validate([
            'chief_complaint' => 'sometimes|string|max:1000',
            'history_present_illness' => 'sometimes|string|max:2000',
            'past_medical_history' => 'sometimes|string|max:1000',
            'family_history' => 'sometimes|string|max:1000',
            'social_history' => 'sometimes|string|max:1000',
            'allergies' => 'sometimes|string|max:500',
            'medications' => 'sometimes|string|max:1000',
            
            // Vital signs
            'temperature' => 'sometimes|numeric|between:30,45',
            'blood_pressure_systolic' => 'sometimes|integer|between:60,250',
            'blood_pressure_diastolic' => 'sometimes|integer|between:40,150',
            'heart_rate' => 'sometimes|integer|between:30,200',
            'respiratory_rate' => 'sometimes|integer|between:8,40',
            'oxygen_saturation' => 'sometimes|integer|between:70,100',
            'height' => 'sometimes|numeric|between:50,250',
            'weight' => 'sometimes|numeric|between:2,300',
            
            // Physical examination
            'physical_examination' => 'sometimes|string|max:2000',
            'assessment' => 'sometimes|string|max:1000',
            'diagnosis' => 'sometimes|string|max:1000',
            'treatment' => 'sometimes|string|max:2000',
            'prescription' => 'sometimes|string|max:1000',
            'follow_up_date' => 'sometimes|date|after:today',
            'notes' => 'sometimes|string|max:1000'
        ]);

        $medicalRecord->update($request->only([
            'chief_complaint', 'history_present_illness', 'past_medical_history',
            'family_history', 'social_history', 'allergies', 'medications',
            'temperature', 'blood_pressure_systolic', 'blood_pressure_diastolic',
            'heart_rate', 'respiratory_rate', 'oxygen_saturation', 'height', 'weight',
            'physical_examination', 'assessment', 'diagnosis', 'treatment',
            'prescription', 'follow_up_date', 'notes'
        ]));

        $medicalRecord->load(['patient', 'doctor', 'appointment']);

        return response()->json([
            'success' => true,
            'message' => 'Medical record updated successfully.',
            'data' => $medicalRecord
        ]);
    }

    /**
     * Remove the specified medical record
     */
    public function destroy($id)
    {
        $medicalRecord = MedicalRecord::find($id);

        if (!$medicalRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Medical record not found.'
            ], 404);
        }

        // Only super admin or the creating doctor can delete medical records
        if (Auth::user()->role !== 'super_admin' && $medicalRecord->created_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this medical record.'
            ], 403);
        }

        $medicalRecord->delete();

        return response()->json([
            'success' => true,
            'message' => 'Medical record deleted successfully.'
        ]);
    }

    /**
     * Get patient's medical history
     */
    public function patientHistory($patientId)
    {
        $patient = Patient::find($patientId);

        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'Patient not found.'
            ], 404);
        }

        $records = MedicalRecord::with(['doctor', 'appointment'])
            ->where('patient_id', $patientId)
            ->orderBy('consultation_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'patient' => $patient,
                'medical_records' => $records,
                'total_visits' => $records->count(),
                'last_visit' => $records->first()?->consultation_date
            ]
        ]);
    }

    /**
     * Get vital signs trend for a patient
     */
    public function vitalsTrend($patientId, Request $request)
    {
        $patient = Patient::find($patientId);

        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'Patient not found.'
            ], 404);
        }

        $months = $request->input('months', 6); // Default 6 months

        $records = MedicalRecord::where('patient_id', $patientId)
            ->where('consultation_date', '>=', now()->subMonths($months))
            ->whereNotNull('temperature')
            ->orWhereNotNull('blood_pressure_systolic')
            ->orWhereNotNull('heart_rate')
            ->orWhereNotNull('weight')
            ->orderBy('consultation_date')
            ->get(['consultation_date', 'temperature', 'blood_pressure_systolic', 
                   'blood_pressure_diastolic', 'heart_rate', 'weight', 'height']);

        return response()->json([
            'success' => true,
            'data' => [
                'patient' => $patient,
                'vitals_history' => $records,
                'period' => "{$months} months"
            ]
        ]);
    }

    /**
     * Get common diagnoses statistics
     */
    public function diagnosisStats(Request $request)
    {
        $months = $request->input('months', 12); // Default 12 months

        $stats = MedicalRecord::where('consultation_date', '>=', now()->subMonths($months))
            ->selectRaw('diagnosis, COUNT(*) as count')
            ->groupBy('diagnosis')
            ->orderBy('count', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => "{$months} months",
                'diagnoses' => $stats
            ]
        ]);
    }

    /**
     * Search medical records by diagnosis
     */
    public function searchByDiagnosis(Request $request)
    {
        $request->validate([
            'diagnosis' => 'required|string|min:3'
        ]);

        $records = MedicalRecord::with(['patient', 'doctor'])
            ->where('diagnosis', 'like', '%' . $request->diagnosis . '%')
            ->orderBy('consultation_date', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $records
        ]);
    }

    /**
     * Get medical record template/form structure
     */
    public function template()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'vital_signs' => [
                    'temperature' => ['unit' => '°C', 'normal_range' => '36.1-37.2'],
                    'blood_pressure' => ['unit' => 'mmHg', 'normal_range' => '90/60-120/80'],
                    'heart_rate' => ['unit' => 'bpm', 'normal_range' => '60-100'],
                    'respiratory_rate' => ['unit' => 'breaths/min', 'normal_range' => '12-20'],
                    'oxygen_saturation' => ['unit' => '%', 'normal_range' => '95-100'],
                    'height' => ['unit' => 'cm', 'normal_range' => 'varies'],
                    'weight' => ['unit' => 'kg', 'normal_range' => 'varies']
                ],
                'common_diagnoses' => [
                    'Upper Respiratory Tract Infection',
                    'Gastritis',
                    'Hypertension',
                    'Diabetes Mellitus',
                    'Headache',
                    'Urinary Tract Infection',
                    'Gastroenteritis',
                    'Allergic Rhinitis',
                    'Bronchitis',
                    'Skin allergy'
                ],
                'consultation_types' => [
                    'consultation',
                    'follow_up',
                    'medical_certificate',
                    'vaccination',
                    'physical_exam'
                ]
            ]
        ]);
    }
}
