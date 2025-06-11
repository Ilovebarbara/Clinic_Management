<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor']);
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('appointment_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('appointment_date', '<=', $request->date_to);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by doctor
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Search by patient name or appointment ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($patientQuery) use ($search) {
                      $patientQuery->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%")
                                  ->orWhere('student_id', 'like', "%{$search}%");
                  });
            });
        }
        
        $appointments = $query->orderBy('appointment_date', 'desc')
                             ->orderBy('appointment_time', 'desc')
                             ->paginate(20);
        
        $doctors = Doctor::all();
        $statuses = ['scheduled', 'completed', 'cancelled', 'no_show'];
        
        return view('appointments.index', compact('appointments', 'doctors', 'statuses'));
    }

    public function create()
    {
        $patients = Patient::orderBy('last_name')->get();
        $doctors = Doctor::where('is_active', true)->orderBy('name')->get();
        
        return view('appointments.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'purpose' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Check if appointment slot is available
        $existingAppointment = Appointment::where([
            'doctor_id' => $validated['doctor_id'],
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time']
        ])->whereIn('status', ['scheduled', 'in_progress'])->first();

        if ($existingAppointment) {
            return redirect()->back()
                           ->withInput()
                           ->withErrors(['appointment_time' => 'This time slot is already booked.']);
        }

        $validated['status'] = 'scheduled';
        $validated['created_by'] = Auth::id();

        $appointment = Appointment::create($validated);

        return redirect()->route('appointments.index')
                        ->with('success', 'Appointment created successfully.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor', 'medicalRecords']);
        
        return view('appointments.show', compact('appointment'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,no_show',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validated['status'] === 'completed' && !$appointment->completed_at) {
            $validated['completed_at'] = now();
        }

        $appointment->update($validated);

        return redirect()->back()->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        if ($appointment->status === 'completed') {
            return redirect()->back()->withErrors(['error' => 'Cannot delete completed appointments.']);
        }

        $appointment->delete();

        return redirect()->route('appointments.index')
                        ->with('success', 'Appointment deleted successfully.');
    }

    public function calendar()
    {
        $appointments = Appointment::with(['patient', 'doctor'])
                                 ->where('appointment_date', '>=', now()->startOfMonth())
                                 ->where('appointment_date', '<=', now()->endOfMonth())
                                 ->get();

        return view('appointments.calendar', compact('appointments'));
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date'
        ]);

        $doctor = Doctor::find($request->doctor_id);
        $date = Carbon::parse($request->date);
        
        // Get booked time slots
        $bookedSlots = Appointment::where('doctor_id', $request->doctor_id)
                                ->whereDate('appointment_date', $request->date)
                                ->whereIn('status', ['scheduled', 'in_progress'])
                                ->pluck('appointment_time')
                                ->map(function($time) {
                                    return Carbon::parse($time)->format('H:i');
                                })
                                ->toArray();

        // Generate available time slots (9 AM to 5 PM, 30-minute intervals)
        $availableSlots = [];
        $start = Carbon::parse('09:00');
        $end = Carbon::parse('17:00');

        while ($start < $end) {
            $timeSlot = $start->format('H:i');
            if (!in_array($timeSlot, $bookedSlots)) {
                $availableSlots[] = $timeSlot;
            }
            $start->addMinutes(30);
        }

        return response()->json([
            'available_slots' => $availableSlots,
            'doctor_name' => $doctor->name
        ]);
    }
}
