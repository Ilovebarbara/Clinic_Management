<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    /**
     * Display a listing of patients
     */
    public function index(Request $request)
    {
        $query = Patient::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('patient_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('patient_type', $request->type);
        }

        // Filter by campus
        if ($request->filled('campus')) {
            $query->where('campus', $request->campus);
        }

        // Filter by college
        if ($request->filled('college')) {
            $query->where('college_office', $request->college);
        }

        // Filter by active status
        if ($request->filled('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($patients);
    }

    /**
     * Store a newly created patient
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:patients,email',
            'password' => 'nullable|min:6',
            'age' => 'required|integer|min:1|max:120',
            'sex' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date',
            'phone' => 'required|string|max:20',
            'lot_number' => 'nullable|string|max:10',
            'barangay_subdivision' => 'required|string|max:100',
            'street' => 'required|string|max:100',
            'city_municipality' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'campus' => 'required|string|max:100',
            'college_office' => 'required|string|max:100',
            'course_designation' => 'required|string|max:200',
            'patient_type' => 'required|in:student,faculty,personnel,non_academic',
            'emergency_contact_name' => 'required|string|max:100',
            'emergency_contact_relation' => 'required|string|max:50',
            'emergency_contact_number' => 'required|string|max:20',
            'blood_type' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'required|string',
        ]);

        // Generate patient number
        $year = date('Y');
        $lastPatient = Patient::where('patient_number', 'like', $year . '-%')
                            ->orderBy('patient_number', 'desc')
                            ->first();
        
        if ($lastPatient) {
            $lastNumber = intval(substr($lastPatient->patient_number, -6));
            $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '000001';
        }
        
        $validated['patient_number'] = $year . '-' . $newNumber;
        
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $validated['created_by_id'] = auth()->id();

        $patient = Patient::create($validated);

        return response()->json([
            'patient' => $patient,
            'message' => 'Patient created successfully'
        ], 201);
    }

    /**
     * Display the specified patient
     */
    public function show(Patient $patient)
    {
        $patient->load(['createdBy', 'appointments', 'medicalRecords', 'queueTickets']);
        
        return response()->json($patient);
    }

    /**
     * Update the specified patient
     */
    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:patients,email,' . $patient->id,
            'age' => 'required|integer|min:1|max:120',
            'sex' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date',
            'phone' => 'required|string|max:20',
            'lot_number' => 'nullable|string|max:10',
            'barangay_subdivision' => 'required|string|max:100',
            'street' => 'required|string|max:100',
            'city_municipality' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'campus' => 'required|string|max:100',
            'college_office' => 'required|string|max:100',
            'course_designation' => 'required|string|max:200',
            'patient_type' => 'required|in:student,faculty,personnel,non_academic',
            'emergency_contact_name' => 'required|string|max:100',
            'emergency_contact_relation' => 'required|string|max:50',
            'emergency_contact_number' => 'required|string|max:20',
            'blood_type' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'required|string',
            'is_active' => 'boolean',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $validated['password'] = Hash::make($request->password);
        }

        $patient->update($validated);

        return response()->json([
            'patient' => $patient,
            'message' => 'Patient updated successfully'
        ]);
    }

    /**
     * Remove the specified patient
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return response()->json([
            'message' => 'Patient deleted successfully'
        ]);
    }

    /**
     * Get patient's medical history
     */
    public function getMedicalHistory(Patient $patient)
    {
        $medicalRecords = $patient->medicalRecords()
                                ->with(['doctor', 'attendingStaff'])
                                ->orderBy('date_time', 'desc')
                                ->paginate(10);

        return response()->json($medicalRecords);
    }

    /**
     * Get patient profile (for mobile app)
     */
    public function getProfile(Request $request)
    {
        $patient = $request->user();
        
        return response()->json($patient);
    }

    /**
     * Update patient profile (for mobile app)
     */
    public function updateProfile(Request $request)
    {
        $patient = $request->user();
        
        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'lot_number' => 'nullable|string|max:10',
            'barangay_subdivision' => 'required|string|max:100',
            'street' => 'required|string|max:100',
            'city_municipality' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'emergency_contact_name' => 'required|string|max:100',
            'emergency_contact_relation' => 'required|string|max:50',
            'emergency_contact_number' => 'required|string|max:20',
            'allergies' => 'required|string',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $validated['password'] = Hash::make($request->password);
        }

        $patient->update($validated);

        return response()->json([
            'patient' => $patient,
            'message' => 'Profile updated successfully'
        ]);
    }

    /**
     * Register patient via kiosk
     */
    public function kioskRegister(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:patients,email',
            'age' => 'required|integer|min:1|max:120',
            'sex' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date',
            'phone' => 'required|string|max:20',
            'barangay_subdivision' => 'required|string|max:100',
            'street' => 'required|string|max:100',
            'city_municipality' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'campus' => 'required|string|max:100',
            'college_office' => 'required|string|max:100',
            'course_designation' => 'required|string|max:200',
            'patient_type' => 'required|in:student,faculty,personnel,non_academic',
            'emergency_contact_name' => 'required|string|max:100',
            'emergency_contact_relation' => 'required|string|max:50',
            'emergency_contact_number' => 'required|string|max:20',
            'blood_type' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'required|string',
        ]);

        // Generate patient number
        $year = date('Y');
        $lastPatient = Patient::where('patient_number', 'like', $year . '-%')
                            ->orderBy('patient_number', 'desc')
                            ->first();
        
        if ($lastPatient) {
            $lastNumber = intval(substr($lastPatient->patient_number, -6));
            $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '000001';
        }
        
        $validated['patient_number'] = $year . '-' . $newNumber;
        
        // Generate temporary password
        $tempPassword = 'temp' . rand(1000, 9999);
        $validated['password'] = Hash::make($tempPassword);

        $patient = Patient::create($validated);

        return response()->json([
            'patient' => $patient,
            'temporary_password' => $tempPassword,
            'message' => 'Patient registered successfully via kiosk'
        ], 201);
    }
}
