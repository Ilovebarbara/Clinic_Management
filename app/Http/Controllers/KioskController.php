<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\QueueTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KioskController extends Controller
{
    public function index()
    {
        return view('kiosk.index');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email',
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
            'service_type' => 'required|in:walk_in,appointment'
        ]);

        DB::beginTransaction();
        try {
            // Create patient
            $patient = Patient::create($validated);

            // Generate queue ticket for walk-in patients
            if ($validated['service_type'] === 'walk_in') {
                $queueTicket = QueueTicket::create([
                    'patient_id' => $patient->id,
                    'queue_number' => $this->generateQueueNumber(),
                    'priority' => $this->calculatePriority($patient),
                    'status' => 'waiting',
                    'service_type' => 'walk_in',
                    'created_at' => now()
                ]);

                DB::commit();

                return redirect()->route('kiosk.print', ['queueNumber' => $queueTicket->queue_number])
                               ->with('success', 'Registration successful! Please take your queue number.');
            }

            DB::commit();

            return redirect()->back()->with('success', 'Registration successful! You can now book appointments.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withInput()
                           ->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }

    public function printQueueNumber($queueNumber)
    {
        $queueTicket = QueueTicket::with('patient')
                                 ->where('queue_number', $queueNumber)
                                 ->firstOrFail();

        // Get current queue position
        $position = QueueTicket::where('status', 'waiting')
                              ->where('priority', '<=', $queueTicket->priority)
                              ->where('created_at', '<=', $queueTicket->created_at)
                              ->count();

        return view('kiosk.print', compact('queueTicket', 'position'));
    }

    public function mobileIndex()
    {
        return view('mobile.index');
    }

    public function queueStatus(Request $request)
    {
        $currentQueue = QueueTicket::with('patient')
                                  ->where('status', 'serving')
                                  ->orderBy('window_number')
                                  ->get();

        $waitingQueue = QueueTicket::with('patient')
                                  ->where('status', 'waiting')
                                  ->orderBy('priority')
                                  ->orderBy('created_at')
                                  ->take(10)
                                  ->get();

        // If user provided queue number, get their position
        $userPosition = null;
        $userQueue = null;
        if ($request->filled('queue_number')) {
            $userQueue = QueueTicket::where('queue_number', $request->queue_number)
                                   ->where('status', 'waiting')
                                   ->first();
            
            if ($userQueue) {
                $userPosition = QueueTicket::where('status', 'waiting')
                                          ->where('priority', '<=', $userQueue->priority)
                                          ->where('created_at', '<=', $userQueue->created_at)
                                          ->count();
            }
        }

        return view('mobile.queue-status', compact('currentQueue', 'waitingQueue', 'userPosition', 'userQueue'));
    }

    private function generateQueueNumber()
    {
        $date = now()->format('Ymd');
        $lastTicket = QueueTicket::where('queue_number', 'like', $date . '%')
                                ->orderBy('queue_number', 'desc')
                                ->first();

        if ($lastTicket) {
            $lastNumber = intval(substr($lastTicket->queue_number, 8));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    private function calculatePriority(Patient $patient)
    {
        // Priority: 1 = Faculty, 2 = Personnel, 3 = Senior Citizens, 4 = Regular
        if ($patient->patient_type === 'faculty') {
            return 1;
        } elseif ($patient->patient_type === 'personnel') {
            return 2;
        } elseif ($patient->age >= 60) {
            return 3;
        } else {
            return 4;
        }
    }
}
