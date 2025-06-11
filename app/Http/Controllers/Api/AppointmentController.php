<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments
     */
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

        // Filter by patient (for patient portal)
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('patient_number', 'like', "%{$search}%");
            });
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
                            ->orderBy('appointment_time', 'desc')
                            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }

    /**
     * Store a newly created appointment
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required|date_format:H:i',
            'reason' => 'required|string|max:500',
            'priority' => 'sometimes|in:emergency,urgent,normal'
        ]);

        // Check if appointment slot is available
        $existingAppointment = Appointment::where('doctor_id', $request->doctor_id)
            ->whereDate('appointment_date', $request->appointment_date)
            ->whereTime('appointment_time', $request->appointment_time)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->first();

        if ($existingAppointment) {
            return response()->json([
                'success' => false,
                'message' => 'This appointment slot is already booked.'
            ], 422);
        }

        // Check if patient already has appointment on same day
        $patientAppointment = Appointment::where('patient_id', $request->patient_id)
            ->whereDate('appointment_date', $request->appointment_date)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->first();

        if ($patientAppointment) {
            return response()->json([
                'success' => false,
                'message' => 'Patient already has an appointment scheduled for this date.'
            ], 422);
        }

        $appointment = Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'reason' => $request->reason,
            'priority' => $request->priority ?? 'normal',
            'status' => 'scheduled',
            'created_by' => Auth::id()
        ]);

        $appointment->load(['patient', 'doctor']);

        return response()->json([
            'success' => true,
            'message' => 'Appointment scheduled successfully.',
            'data' => $appointment
        ], 201);
    }

    /**
     * Display the specified appointment
     */
    public function show($id)
    {
        $appointment = Appointment::with(['patient', 'doctor', 'medicalRecord'])->find($id);

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $appointment
        ]);
    }

    /**
     * Update the specified appointment
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found.'
            ], 404);
        }

        $request->validate([
            'doctor_id' => 'sometimes|exists:doctors,id',
            'appointment_date' => 'sometimes|date',
            'appointment_time' => 'sometimes|date_format:H:i',
            'reason' => 'sometimes|string|max:500',
            'priority' => 'sometimes|in:emergency,urgent,normal',
            'status' => 'sometimes|in:scheduled,confirmed,in_progress,completed,cancelled,no_show',
            'notes' => 'sometimes|string'
        ]);

        // Check if rescheduling and slot is available
        if ($request->filled('appointment_date') || $request->filled('appointment_time')) {
            $date = $request->appointment_date ?? $appointment->appointment_date;
            $time = $request->appointment_time ?? $appointment->appointment_time;
            
            $existingAppointment = Appointment::where('doctor_id', $request->doctor_id ?? $appointment->doctor_id)
                ->where('id', '!=', $id)
                ->whereDate('appointment_date', $date)
                ->whereTime('appointment_time', $time)
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->first();

            if ($existingAppointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'This appointment slot is already booked.'
                ], 422);
            }
        }

        $appointment->update($request->only([
            'doctor_id', 'appointment_date', 'appointment_time', 
            'reason', 'priority', 'status', 'notes'
        ]));

        $appointment->load(['patient', 'doctor']);

        return response()->json([
            'success' => true,
            'message' => 'Appointment updated successfully.',
            'data' => $appointment
        ]);
    }

    /**
     * Remove the specified appointment
     */
    public function destroy($id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found.'
            ], 404);
        }

        // Only allow deletion of scheduled appointments
        if (!in_array($appointment->status, ['scheduled', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete appointment with status: ' . $appointment->status
            ], 422);
        }

        $appointment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Appointment deleted successfully.'
        ]);
    }

    /**
     * Get available appointment slots for a doctor on a specific date
     */
    public function availableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after:today'
        ]);

        $doctor = Doctor::find($request->doctor_id);
        $date = Carbon::parse($request->date);

        // Define clinic hours (8 AM to 5 PM with 1-hour lunch break at 12 PM)
        $slots = [];
        $startTime = 8; // 8 AM
        $endTime = 17;  // 5 PM
        $lunchStart = 12; // 12 PM
        $lunchEnd = 13;   // 1 PM

        for ($hour = $startTime; $hour < $endTime; $hour++) {
            // Skip lunch hour
            if ($hour >= $lunchStart && $hour < $lunchEnd) {
                continue;
            }

            $timeSlot = sprintf('%02d:00', $hour);
            
            // Check if slot is already booked
            $isBooked = Appointment::where('doctor_id', $request->doctor_id)
                ->whereDate('appointment_date', $request->date)
                ->whereTime('appointment_time', $timeSlot)
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->exists();

            if (!$isBooked) {
                $slots[] = [
                    'time' => $timeSlot,
                    'formatted_time' => Carbon::createFromFormat('H:i', $timeSlot)->format('g:i A'),
                    'available' => true
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'doctor' => $doctor,
                'date' => $date->format('Y-m-d'),
                'slots' => $slots
            ]
        ]);
    }

    /**
     * Confirm an appointment
     */
    public function confirm($id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found.'
            ], 404);
        }

        if ($appointment->status !== 'scheduled') {
            return response()->json([
                'success' => false,
                'message' => 'Only scheduled appointments can be confirmed.'
            ], 422);
        }

        $appointment->update(['status' => 'confirmed']);

        return response()->json([
            'success' => true,
            'message' => 'Appointment confirmed successfully.',
            'data' => $appointment
        ]);
    }

    /**
     * Cancel an appointment
     */
    public function cancel(Request $request, $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found.'
            ], 404);
        }

        if (!in_array($appointment->status, ['scheduled', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel appointment with status: ' . $appointment->status
            ], 422);
        }

        $appointment->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->input('reason', 'No reason provided'),
            'cancelled_at' => now(),
            'cancelled_by' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment cancelled successfully.',
            'data' => $appointment
        ]);
    }

    /**
     * Get appointments for today
     */
    public function today()
    {
        $appointments = Appointment::with(['patient', 'doctor'])
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }

    /**
     * Get upcoming appointments
     */
    public function upcoming(Request $request)
    {
        $days = $request->input('days', 7); // Default 7 days

        $appointments = Appointment::with(['patient', 'doctor'])
            ->where('appointment_date', '>', today())
            ->where('appointment_date', '<=', today()->addDays($days))
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }
}
