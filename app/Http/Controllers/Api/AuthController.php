<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Staff login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account is inactive. Please contact an administrator.'],
            ]);
        }

        $token = $user->createToken('clinic-app')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'user_type' => 'staff'
        ]);
    }

    /**
     * Patient login
     */
    public function patientLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $patient = Patient::where('email', $request->email)->first();

        if (!$patient || !Hash::check($request->password, $patient->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$patient->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account is inactive. Please contact the clinic.'],
            ]);
        }

        $token = $patient->createToken('clinic-patient-app')->plainTextToken;

        return response()->json([
            'patient' => $patient,
            'token' => $token,
            'user_type' => 'patient'
        ]);
    }

    /**
     * Patient registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:patients,email',
            'password' => 'required|min:6|confirmed',
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
        $validated['password'] = Hash::make($validated['password']);

        $patient = Patient::create($validated);

        $token = $patient->createToken('clinic-patient-app')->plainTextToken;

        return response()->json([
            'patient' => $patient,
            'token' => $token,
            'user_type' => 'patient',
            'message' => 'Registration successful'
        ], 201);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'user_type' => $request->user() instanceof Patient ? 'patient' : 'staff'
        ]);
    }

    /**
     * Get all users (admin only)
     */
    public function getUsers()
    {
        $users = User::with(['createdBy'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);

        return response()->json($users);
    }

    /**
     * Create new user (admin only)
     */
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:64|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'role' => 'required|in:super_admin,staff,nurse,dentist,physician',
            'phone' => 'nullable|string|max:20',
            'employee_id' => 'nullable|string|max:20|unique:users',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['created_by_id'] = Auth::id();

        $user = User::create($validated);

        return response()->json([
            'user' => $user,
            'message' => 'User created successfully'
        ], 201);
    }

    /**
     * Update user (admin only)
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:64|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'role' => 'required|in:super_admin,staff,nurse,dentist,physician',
            'phone' => 'nullable|string|max:20',
            'employee_id' => 'nullable|string|max:20|unique:users,employee_id,' . $user->id,
            'is_active' => 'boolean',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return response()->json([
            'user' => $user,
            'message' => 'User updated successfully'
        ]);
    }

    /**
     * Delete user (admin only)
     */
    public function deleteUser(User $user)
    {
        if ($user->role === 'super_admin' && User::where('role', 'super_admin')->count() <= 1) {
            return response()->json([
                'message' => 'Cannot delete the last super admin user'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
