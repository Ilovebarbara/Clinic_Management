<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Patient;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle staff login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string', // Can be username or email
            'password' => 'required|string',
        ]);

        // Try to login with username or email
        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $attemptCredentials = [
            $loginField => $credentials['login'],
            'password' => $credentials['password'],
            'is_active' => true,
        ];

        if (Auth::attempt($attemptCredentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Log the login
            activity()
                ->performedOn(Auth::user())
                ->withProperties(['ip' => $request->ip()])
                ->log('User logged in');

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records or your account is inactive.',
        ])->onlyInput('login');
    }

    /**
     * Show patient login form
     */
    public function showPatientLoginForm()
    {
        return view('auth.patient-login');
    }

    /**
     * Handle patient login
     */
    public function patientLogin(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string', // Can be patient_number or email
            'password' => 'required|string',
        ]);

        // Determine login field
        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'patient_number';
        
        $patient = Patient::where($loginField, $credentials['login'])
                         ->where('is_active', true)
                         ->first();

        if ($patient && Hash::check($credentials['password'], $patient->password)) {
            Auth::guard('patient')->login($patient, $request->filled('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('patient.dashboard'));
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records or your account is inactive.',
        ])->onlyInput('login');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        // Log the logout
        if (Auth::check()) {
            activity()
                ->performedOn(Auth::user())
                ->withProperties(['ip' => $request->ip()])
                ->log('User logged out');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Handle patient logout
     */
    public function patientLogout(Request $request)
    {
        Auth::guard('patient')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('patient.login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show registration form (Super Admin only)
     */
    public function showRegistrationForm()
    {
        $this->authorize('create', User::class);
        
        return view('auth.register');
    }

    /**
     * Handle staff registration (Super Admin only)
     */
    public function register(Request $request)
    {
        $this->authorize('create', User::class);

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:64|unique:users',
            'email' => 'required|email|max:120|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'role' => 'required|in:staff,nurse,dentist,physician',
            'phone' => 'nullable|string|max:20',
            'employee_id' => 'nullable|string|max:20|unique:users',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'role' => $request->role,
            'phone' => $request->phone,
            'employee_id' => $request->employee_id,
            'created_by_id' => Auth::id(),
            'is_active' => true,
        ]);

        // Log the user creation
        activity()
            ->performedOn($user)
            ->causedBy(Auth::user())
            ->log('Staff user created');

        return redirect()->route('admin.staff.index')
                        ->with('success', 'Staff member registered successfully.');
    }

    /**
     * Show patient registration form
     */
    public function showPatientRegistrationForm()
    {
        $bloodTypes = Patient::BLOOD_TYPES;
        $campusOptions = Patient::CAMPUS_OPTIONS;
        $collegeOfficeOptions = Patient::COLLEGE_OFFICE_OPTIONS;
        $patientTypes = Patient::PATIENT_TYPES;

        return view('auth.patient-register', compact(
            'bloodTypes',
            'campusOptions', 
            'collegeOfficeOptions',
            'patientTypes'
        ));
    }

    /**
     * Handle patient registration
     */
    public function patientRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Basic Information
            'patient_number' => 'required|string|max:20|unique:patients',
            'email' => 'required|email|max:120|unique:patients',
            'password' => 'required|string|min:6|confirmed',
            
            // Personal Information
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'age' => 'required|integer|min:1|max:150',
            'sex' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date|before:today',
            
            // Contact Information
            'phone' => 'required|string|max:20',
            
            // Address
            'lot_number' => 'nullable|string|max:20',
            'barangay_subdivision' => 'nullable|string|max:100',
            'street' => 'nullable|string|max:100',
            'city_municipality' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            
            // Academic/Work Information
            'campus' => 'required|string|max:50',
            'college_office' => 'required|string|max:100',
            'course_designation' => 'required|string|max:100',
            'patient_type' => 'required|in:student,faculty,non_academic',
            
            // Emergency Contact
            'emergency_contact_name' => 'required|string|max:100',
            'emergency_contact_relation' => 'required|string|max:50',
            'emergency_contact_number' => 'required|string|max:20',
            
            // Medical Information
            'blood_type' => 'nullable|in:' . implode(',', Patient::BLOOD_TYPES),
            'allergies' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $patient = Patient::create([
            'patient_number' => $request->patient_number,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'age' => $request->age,
            'sex' => $request->sex,
            'date_of_birth' => $request->date_of_birth,
            'phone' => $request->phone,
            'lot_number' => $request->lot_number,
            'barangay_subdivision' => $request->barangay_subdivision,
            'street' => $request->street,
            'city_municipality' => $request->city_municipality,
            'province' => $request->province,
            'campus' => $request->campus,
            'college_office' => $request->college_office,
            'course_designation' => $request->course_designation,
            'patient_type' => $request->patient_type,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_relation' => $request->emergency_contact_relation,
            'emergency_contact_number' => $request->emergency_contact_number,
            'blood_type' => $request->blood_type,
            'allergies' => $request->allergies,
            'is_active' => true,
        ]);

        // Log the patient registration
        activity()
            ->performedOn($patient)
            ->withProperties(['ip' => $request->ip()])
            ->log('Patient registered');

        // Auto-login the patient
        Auth::guard('patient')->login($patient);

        return redirect()->route('patient.dashboard')
                        ->with('success', 'Registration successful! Welcome to the clinic portal.');
    }
}
