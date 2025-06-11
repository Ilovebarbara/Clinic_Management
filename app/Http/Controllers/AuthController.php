<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
            'user_type' => 'required|in:staff,patient'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput($request->except('password'));
        }

        $login = $request->input('login');
        $password = $request->input('password');
        $userType = $request->input('user_type');

        if ($userType === 'staff') {
            return $this->loginStaff($login, $password, $request);
        } else {
            return $this->loginPatient($login, $password, $request);
        }
    }

    private function loginStaff($login, $password, $request)
    {
        // Try to find user by username or email
        $user = User::where('username', $login)
                   ->orWhere('email', $login)
                   ->first();

        if (!$user) {
            return redirect()->back()
                           ->withErrors(['login' => 'Invalid credentials.'])
                           ->withInput($request->except('password'));
        }

        if (!$user->is_active) {
            return redirect()->back()
                           ->withErrors(['login' => 'Account is inactive. Please contact administrator.'])
                           ->withInput($request->except('password'));
        }

        if (!Hash::check($password, $user->password)) {
            return redirect()->back()
                           ->withErrors(['login' => 'Invalid credentials.'])
                           ->withInput($request->except('password'));
        }

        // Log in the user
        Auth::login($user, $request->filled('remember'));

        // Update last login
        $user->update(['last_login_at' => now()]);

        return redirect()->intended(route('dashboard'))
                        ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    private function loginPatient($login, $password, $request)
    {
        // Try to find patient by email or student_id
        $patient = Patient::where('email', $login)
                         ->orWhere('student_id', $login)
                         ->first();

        if (!$patient) {
            return redirect()->back()
                           ->withErrors(['login' => 'Invalid credentials.'])
                           ->withInput($request->except('password'));
        }

        if (!Hash::check($password, $patient->password)) {
            return redirect()->back()
                           ->withErrors(['login' => 'Invalid credentials.'])
                           ->withInput($request->except('password'));
        }

        // Log in the patient using a custom guard or session
        session(['patient_id' => $patient->id, 'patient_name' => $patient->full_name]);

        // Update last login
        $patient->update(['last_login_at' => now()]);

        return redirect()->route('mobile.index')
                        ->with('success', 'Welcome back, ' . $patient->first_name . '!');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
        }
        
        // Clear patient session
        $request->session()->forget(['patient_id', 'patient_name']);
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
                        ->with('success', 'You have been logged out successfully.');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:patients',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string|max:500',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'patient_type' => 'required|in:student,faculty,personnel,non_academic',
            'student_id' => 'nullable|string|max:20|unique:patients,student_id',
            'campus' => 'required|string|max:100',
            'college' => 'required|string|max:100',
            'blood_type' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string|max:500',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput($request->except('password', 'password_confirmation'));
        }

        $data = $validator->validated();
        $data['password'] = Hash::make($data['password']);

        $patient = Patient::create($data);

        // Auto-login after registration
        session(['patient_id' => $patient->id, 'patient_name' => $patient->full_name]);

        return redirect()->route('mobile.index')
                        ->with('success', 'Registration successful! Welcome to the clinic.');
    }

    public function createSuperAdmin(Request $request)
    {
        // Check if super admin already exists
        if (User::where('role', 'super_admin')->exists()) {
            return redirect()->route('login')
                           ->withErrors(['error' => 'Super admin already exists.']);
        }

        if ($request->isMethod('get')) {
            return view('auth.create-super-admin');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput($request->except('password', 'password_confirmation'));
        }

        $data = $validator->validated();
        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'super_admin';
        $data['is_active'] = true;

        User::create($data);

        return redirect()->route('login')
                        ->with('success', 'Super admin account created successfully. You can now login.');
    }
}
