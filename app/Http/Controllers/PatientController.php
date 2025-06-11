<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\QueueTicket;

class PatientController extends Controller
{
    /**
     * Display a listing of patients
     */
    public function index(Request $request)
    {
        $query = Patient::query()->with(['createdBy']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('patient_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by patient type
        if ($request->filled('patient_type')) {
            $query->where('patient_type', $request->patient_type);
        }

        // Filter by campus
        if ($request->filled('campus')) {
            $query->where('campus', $request->campus);
        }

        // Filter by college/office
        if ($request->filled('college_office')) {
            $query->where('college_office', $request->college_office);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $patients = $query->orderBy('last_name')
                         ->orderBy('first_name')
                         ->paginate(20)
                         ->withQueryString();

        return view('patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new patient
     */
    public function create()
    {
        $this->authorize('create', Patient::class);

        return view('patients.create', [
            'bloodTypes' => Patient::BLOOD_TYPES,
            'campusOptions' => Patient::CAMPUS_OPTIONS,
            'collegeOfficeOptions' => Patient::COLLEGE_OFFICE_OPTIONS,
            'patientTypes' => Patient::PATIENT_TYPES,
        ]);
    }

    /**
     * Store a newly created patient
     */
    public function store(Request $request)
    {
        $this->authorize('create', Patient::class);

        $validated = $request->validate([
            'patient_number' => 'required|string|max:20|unique:patients',
            'email' => 'required|email|max:120|unique:patients',
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'age' => 'required|integer|min:1|max:150',
            'sex' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date|before:today',
            'phone' => 'required|string|max:20',
            'lot_number' => 'nullable|string|max:20',
            'barangay_subdivision' => 'nullable|string|max:100',
            'street' => 'nullable|string|max:100',
            'city_municipality' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'campus' => 'required|string|max:50',
            'college_office' => 'required|string|max:100',
            'course_designation' => 'required|string|max:100',
            'patient_type' => 'required|in:student,faculty,non_academic',
            'emergency_contact_name' => 'required|string|max:100',
            'emergency_contact_relation' => 'required|string|max:50',
            'emergency_contact_number' => 'required|string|max:20',
            'blood_type' => 'nullable|in:' . implode(',', Patient::BLOOD_TYPES),
            'allergies' => 'nullable|string',
        ]);

        $validated['created_by_id'] = Auth::id();
        $validated['is_active'] = true;

        $patient = Patient::create($validated);

        return redirect()->route('patients.show', $patient)
                        ->with('success', 'Patient created successfully.');
    }

    /**
     * Display the specified patient
     */
    public function show(Patient $patient)
    {
        $patient->load(['createdBy', 'appointments.doctor', 'medicalRecords.doctor', 'queueTickets']);

        $appointments = $patient->appointments()->latest()->take(10)->get();
        $medicalRecords = $patient->medicalRecords()->latest()->take(10)->get();
        $certificates = $patient->certificates()->latest()->take(5)->get();

        return view('patients.show', compact('patient', 'appointments', 'medicalRecords', 'certificates'));
    }

    /**
     * Show the form for editing the patient
     */
    public function edit(Patient $patient)
    {
        $this->authorize('update', $patient);

        return view('patients.edit', [
            'patient' => $patient,
            'bloodTypes' => Patient::BLOOD_TYPES,
            'campusOptions' => Patient::CAMPUS_OPTIONS,
            'collegeOfficeOptions' => Patient::COLLEGE_OFFICE_OPTIONS,
            'patientTypes' => Patient::PATIENT_TYPES,
        ]);
    }

    /**
     * Update the specified patient
     */
    public function update(Request $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $validated = $request->validate([
            'patient_number' => 'required|string|max:20|unique:patients,patient_number,' . $patient->id,
            'email' => 'required|email|max:120|unique:patients,email,' . $patient->id,
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'age' => 'required|integer|min:1|max:150',
            'sex' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date|before:today',
            'phone' => 'required|string|max:20',
            'lot_number' => 'nullable|string|max:20',
            'barangay_subdivision' => 'nullable|string|max:100',
            'street' => 'nullable|string|max:100',
            'city_municipality' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'campus' => 'required|string|max:50',
            'college_office' => 'required|string|max:100',
            'course_designation' => 'required|string|max:100',
            'patient_type' => 'required|in:student,faculty,non_academic',
            'emergency_contact_name' => 'required|string|max:100',
            'emergency_contact_relation' => 'required|string|max:50',
            'emergency_contact_number' => 'required|string|max:20',
            'blood_type' => 'nullable|in:' . implode(',', Patient::BLOOD_TYPES),
            'allergies' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $patient->update($validated);

        return redirect()->route('patients.show', $patient)
                        ->with('success', 'Patient updated successfully.');
    }

    /**
     * Remove the specified patient
     */
    public function destroy(Patient $patient)
    {
        $this->authorize('delete', $patient);

        // Soft delete by deactivating
        $patient->update(['is_active' => false]);

        return redirect()->route('patients.index')
                        ->with('success', 'Patient deactivated successfully.');
    }

    /**
     * Search patients for dropdowns/autocomplete
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $patients = Patient::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('patient_number', 'like', "%{$query}%");
            })
            ->select('id', 'patient_number', 'first_name', 'middle_name', 'last_name', 'email')
            ->limit(10)
            ->get()
            ->map(function($patient) {
                return [
                    'id' => $patient->id,
                    'text' => "{$patient->full_name} ({$patient->patient_number})",
                    'patient_number' => $patient->patient_number,
                    'email' => $patient->email,
                ];
            });

        return response()->json($patients);
    }

    /**
     * Get patient statistics
     */
    public function stats()
    {
        $stats = [
            'total' => Patient::count(),
            'active' => Patient::where('is_active', true)->count(),
            'students' => Patient::where('patient_type', 'student')->count(),
            'faculty' => Patient::where('patient_type', 'faculty')->count(),
            'non_academic' => Patient::where('patient_type', 'non_academic')->count(),
            'new_this_month' => Patient::whereMonth('created_at', now()->month)->count(),
            'by_campus' => Patient::groupBy('campus')->selectRaw('campus, count(*) as count')->pluck('count', 'campus'),
            'by_college' => Patient::groupBy('college_office')->selectRaw('college_office, count(*) as count')->orderBy('count', 'desc')->limit(10)->pluck('count', 'college_office'),
        ];

        return response()->json($stats);
    }

    /**
     * Export patients to CSV
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', Patient::class);

        $query = Patient::query();

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('patient_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('patient_type')) {
            $query->where('patient_type', $request->patient_type);
        }

        $patients = $query->orderBy('last_name')->get();

        $filename = 'patients_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($patients) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Patient Number', 'First Name', 'Middle Name', 'Last Name', 'Email', 'Phone',
                'Age', 'Sex', 'Date of Birth', 'Patient Type', 'Campus', 'College/Office',
                'Course/Designation', 'Blood Type', 'Emergency Contact', 'Created At'
            ]);

            foreach ($patients as $patient) {
                fputcsv($file, [
                    $patient->patient_number,
                    $patient->first_name,
                    $patient->middle_name,
                    $patient->last_name,
                    $patient->email,
                    $patient->phone,
                    $patient->age,
                    $patient->sex,
                    $patient->date_of_birth->format('Y-m-d'),
                    $patient->patient_type,
                    $patient->campus,
                    $patient->college_office,
                    $patient->course_designation,
                    $patient->blood_type,
                    $patient->emergency_contact_name,
                    $patient->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
