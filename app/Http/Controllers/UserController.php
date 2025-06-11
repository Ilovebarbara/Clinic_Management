<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }
        
        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $roles = ['super_admin', 'admin', 'doctor', 'nurse', 'staff'];
        
        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = ['admin', 'doctor', 'nurse', 'staff'];
        
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,doctor,nurse,staff',
            'phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;
        $validated['created_by'] = Auth::id();

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')
                                                ->store('profile-images', 'public');
        }

        User::create($validated);

        return redirect()->route('users.index')
                        ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        // Prevent editing super admin accounts (except by super admin)
        if ($user->role === 'super_admin' && Auth::user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $roles = Auth::user()->role === 'super_admin' 
               ? ['super_admin', 'admin', 'doctor', 'nurse', 'staff']
               : ['admin', 'doctor', 'nurse', 'staff'];
        
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Prevent editing super admin accounts (except by super admin)
        if ($user->role === 'super_admin' && Auth::user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:super_admin,admin,doctor,nurse,staff',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            
            $validated['profile_image'] = $request->file('profile_image')
                                                ->store('profile-images', 'public');
        }

        $user->update($validated);

        return redirect()->route('users.index')
                        ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deletion of super admin accounts
        if ($user->role === 'super_admin') {
            return redirect()->back()
                           ->withErrors(['error' => 'Cannot delete super admin accounts.']);
        }

        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            return redirect()->back()
                           ->withErrors(['error' => 'You cannot delete your own account.']);
        }

        // Delete profile image
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->delete();

        return redirect()->route('users.index')
                        ->with('success', 'User deleted successfully.');
    }

    public function profile()
    {
        $user = Auth::user();
        
        return view('users.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20'
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                           ->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    public function updateImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = Auth::user();

        // Delete old image
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        // Store new image
        $imagePath = $request->file('profile_image')
                           ->store('profile-images', 'public');

        $user->update(['profile_image' => $imagePath]);

        return redirect()->back()->with('success', 'Profile image updated successfully.');
    }
}
