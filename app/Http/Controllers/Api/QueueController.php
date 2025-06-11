<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QueueTicket;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QueueController extends Controller
{
    /**
     * Display the current queue status
     */
    public function index(Request $request)
    {
        $query = QueueTicket::with(['patient']);

        // Filter by date (default to today)
        $date = $request->input('date', today()->toDateString());
        $query->whereDate('created_at', $date);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by service type
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        // Filter by window
        if ($request->filled('window_number')) {
            $query->where('window_number', $request->window_number);
        }

        $tickets = $query->orderBy('priority')
                        ->orderBy('created_at')
                        ->get();

        // Group tickets by status for easier frontend handling
        $grouped = $tickets->groupBy('status');

        return response()->json([
            'success' => true,
            'data' => [
                'tickets' => $tickets,
                'grouped' => $grouped,
                'statistics' => [
                    'total' => $tickets->count(),
                    'waiting' => $grouped->get('waiting', collect())->count(),
                    'serving' => $grouped->get('serving', collect())->count(),
                    'completed' => $grouped->get('completed', collect())->count(),
                    'cancelled' => $grouped->get('cancelled', collect())->count(),
                ]
            ]
        ]);
    }

    /**
     * Create a new queue ticket
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'service_type' => 'required|in:consultation,follow_up,medical_certificate,vaccination,physical_exam',
            'reason' => 'sometimes|string|max:500',
            'is_emergency' => 'sometimes|boolean'
        ]);

        // Check if patient already has active ticket today
        $existingTicket = QueueTicket::where('patient_id', $request->patient_id)
            ->whereDate('created_at', today())
            ->whereIn('status', ['waiting', 'serving'])
            ->first();

        if ($existingTicket) {
            return response()->json([
                'success' => false,
                'message' => 'Patient already has an active queue ticket for today.',
                'data' => $existingTicket
            ], 422);
        }

        $patient = Patient::find($request->patient_id);

        // Generate ticket number
        $todayCount = QueueTicket::whereDate('created_at', today())->count();
        $ticketNumber = sprintf('%s-%03d', today()->format('Ymd'), $todayCount + 1);

        // Calculate priority
        $priority = $this->calculatePriority($patient, $request->boolean('is_emergency', false));

        $ticket = QueueTicket::create([
            'ticket_number' => $ticketNumber,
            'patient_id' => $request->patient_id,
            'service_type' => $request->service_type,
            'priority' => $priority,
            'status' => 'waiting',
            'reason' => $request->input('reason'),
            'is_emergency' => $request->boolean('is_emergency', false),
            'created_by' => Auth::id()
        ]);

        $ticket->load('patient');

        return response()->json([
            'success' => true,
            'message' => 'Queue ticket created successfully.',
            'data' => $ticket
        ], 201);
    }

    /**
     * Display the specified queue ticket
     */
    public function show($id)
    {
        $ticket = QueueTicket::with(['patient', 'servedBy'])->find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Queue ticket not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ticket
        ]);
    }

    /**
     * Update the specified queue ticket
     */
    public function update(Request $request, $id)
    {
        $ticket = QueueTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Queue ticket not found.'
            ], 404);
        }

        $request->validate([
            'status' => 'sometimes|in:waiting,serving,completed,cancelled,no_show',
            'window_number' => 'sometimes|integer|min:1|max:10',
            'served_by' => 'sometimes|exists:users,id',
            'notes' => 'sometimes|string|max:1000'
        ]);

        // Handle status transitions
        $oldStatus = $ticket->status;
        $newStatus = $request->input('status', $oldStatus);

        if ($newStatus !== $oldStatus) {
            switch ($newStatus) {
                case 'serving':
                    $ticket->served_at = now();
                    $ticket->served_by = $request->input('served_by', Auth::id());
                    $ticket->window_number = $request->input('window_number');
                    break;

                case 'completed':
                    if ($oldStatus !== 'serving') {
                        return response()->json([
                            'success' => false,
                            'message' => 'Ticket must be in serving status before completion.'
                        ], 422);
                    }
                    $ticket->completed_at = now();
                    break;

                case 'cancelled':
                case 'no_show':
                    $ticket->cancelled_at = now();
                    break;
            }
        }

        $ticket->update($request->only([
            'status', 'window_number', 'served_by', 'notes'
        ]));

        $ticket->load(['patient', 'servedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Queue ticket updated successfully.',
            'data' => $ticket
        ]);
    }

    /**
     * Call the next patient in queue
     */
    public function callNext(Request $request)
    {
        $request->validate([
            'window_number' => 'required|integer|min:1|max:10',
            'service_type' => 'sometimes|in:consultation,follow_up,medical_certificate,vaccination,physical_exam'
        ]);

        $query = QueueTicket::with(['patient'])
            ->where('status', 'waiting')
            ->whereDate('created_at', today());

        // Filter by service type if specified
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        $nextTicket = $query->orderBy('priority')
                           ->orderBy('created_at')
                           ->first();

        if (!$nextTicket) {
            return response()->json([
                'success' => false,
                'message' => 'No patients waiting in queue.'
            ], 404);
        }

        // Update ticket status to serving
        $nextTicket->update([
            'status' => 'serving',
            'window_number' => $request->window_number,
            'served_by' => Auth::id(),
            'served_at' => now()
        ]);

        $nextTicket->load(['patient', 'servedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Next patient called successfully.',
            'data' => $nextTicket
        ]);
    }

    /**
     * Get queue statistics for dashboard
     */
    public function statistics(Request $request)
    {
        $date = $request->input('date', today()->toDateString());

        $stats = QueueTicket::whereDate('created_at', $date)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "waiting" THEN 1 ELSE 0 END) as waiting,
                SUM(CASE WHEN status = "serving" THEN 1 ELSE 0 END) as serving,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN status = "no_show" THEN 1 ELSE 0 END) as no_show,
                AVG(CASE WHEN completed_at IS NOT NULL AND served_at IS NOT NULL 
                    THEN TIMESTAMPDIFF(MINUTE, served_at, completed_at) END) as avg_service_time
            ')
            ->first();

        // Get hourly distribution
        $hourlyStats = QueueTicket::whereDate('created_at', $date)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Get service type distribution
        $serviceStats = QueueTicket::whereDate('created_at', $date)
            ->selectRaw('service_type, COUNT(*) as count')
            ->groupBy('service_type')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'overall' => $stats,
                'hourly' => $hourlyStats,
                'by_service' => $serviceStats
            ]
        ]);
    }

    /**
     * Get patient's queue position
     */
    public function position($ticketId)
    {
        $ticket = QueueTicket::find($ticketId);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Queue ticket not found.'
            ], 404);
        }

        if ($ticket->status !== 'waiting') {
            return response()->json([
                'success' => true,
                'data' => [
                    'ticket' => $ticket,
                    'position' => 0,
                    'status' => $ticket->status
                ]
            ]);
        }

        // Count tickets ahead in queue
        $position = QueueTicket::where('status', 'waiting')
            ->whereDate('created_at', today())
            ->where(function ($query) use ($ticket) {
                $query->where('priority', '<', $ticket->priority)
                      ->orWhere(function ($q) use ($ticket) {
                          $q->where('priority', $ticket->priority)
                            ->where('created_at', '<', $ticket->created_at);
                      });
            })
            ->count() + 1;

        return response()->json([
            'success' => true,
            'data' => [
                'ticket' => $ticket,
                'position' => $position,
                'estimated_wait' => $position * 15, // Assuming 15 minutes per patient
                'status' => $ticket->status
            ]
        ]);
    }

    /**
     * Cancel a queue ticket
     */
    public function cancel($id, Request $request)
    {
        $ticket = QueueTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Queue ticket not found.'
            ], 404);
        }

        if (!in_array($ticket->status, ['waiting', 'serving'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel ticket with status: ' . $ticket->status
            ], 422);
        }

        $ticket->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'notes' => $request->input('reason', 'Cancelled by user')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Queue ticket cancelled successfully.',
            'data' => $ticket
        ]);
    }

    /**
     * Get currently serving tickets
     */
    public function currentlyServing()
    {
        $serving = QueueTicket::with(['patient', 'servedBy'])
            ->where('status', 'serving')
            ->whereDate('created_at', today())
            ->orderBy('window_number')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $serving
        ]);
    }

    /**
     * Calculate priority based on patient type and emergency status
     */
    private function calculatePriority(Patient $patient, bool $isEmergency = false)
    {
        if ($isEmergency) {
            return 0; // Highest priority for emergencies
        }

        // Priority based on patient type and special conditions
        if ($patient->patient_type === 'faculty') {
            return 1;
        }

        if ($patient->patient_type === 'personnel') {
            return 2;
        }

        // Check if senior citizen (assuming birth_date field exists)
        if ($patient->birth_date && Carbon::parse($patient->birth_date)->age >= 60) {
            return 3;
        }

        // Check if PWD (Person with Disability)
        if (isset($patient->is_pwd) && $patient->is_pwd) {
            return 3;
        }

        // Regular students and others
        return 4;
    }

    /**
     * Reset queue (for testing purposes or end of day cleanup)
     */
    public function reset(Request $request)
    {
        $date = $request->input('date', today()->toDateString());

        $updated = QueueTicket::whereDate('created_at', $date)
            ->whereIn('status', ['waiting', 'serving'])
            ->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'notes' => 'Queue reset by administrator'
            ]);

        return response()->json([
            'success' => true,
            'message' => "Queue reset successfully. {$updated} tickets were cancelled.",
            'data' => ['cancelled_count' => $updated]
        ]);
    }
}
