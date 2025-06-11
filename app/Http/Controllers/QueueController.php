<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\QueueTicket;
use App\Models\Patient;

class QueueController extends Controller
{
    /**
     * Display the queue management interface
     */
    public function management()
    {
        $this->authorize('viewAny', QueueTicket::class);

        $currentlyServing = QueueTicket::serving()
            ->with('patient')
            ->get();

        $waitingQueue = QueueTicket::waiting()
            ->byPriority()
            ->with('patient')
            ->get();

        $todayStats = [
            'total_tickets' => QueueTicket::today()->count(),
            'completed' => QueueTicket::today()->where('status', 'completed')->count(),
            'waiting' => QueueTicket::waiting()->count(),
            'average_wait_time' => $this->calculateAverageWaitTime(),
        ];

        $windows = [1, 2, 3, 4]; // Service windows

        return view('queue.management', compact(
            'currentlyServing',
            'waitingQueue', 
            'todayStats',
            'windows'
        ));
    }

    /**
     * Display queue status for public screens
     */
    public function display()
    {
        $currentlyServing = QueueTicket::serving()
            ->with('patient')
            ->get();

        $nextInQueue = QueueTicket::waiting()
            ->byPriority()
            ->with('patient')
            ->take(10)
            ->get();

        $stats = [
            'total_waiting' => QueueTicket::waiting()->count(),
            'average_wait' => $this->calculateAverageWaitTime(),
        ];

        return view('queue.display', compact('currentlyServing', 'nextInQueue', 'stats'));
    }

    /**
     * Show the kiosk interface for generating tickets
     */
    public function kiosk()
    {
        $patients = Patient::active()
            ->orderBy('last_name')
            ->select('id', 'patient_number', 'first_name', 'last_name', 'patient_type')
            ->get();

        $transactionTypes = QueueTicket::TRANSACTION_TYPES;
        
        return view('queue.kiosk', compact('patients', 'transactionTypes'));
    }

    /**
     * Generate a new queue ticket
     */
    public function generateTicket(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'nullable|exists:patients,id',
            'transaction_type' => 'required|in:' . implode(',', array_keys(QueueTicket::TRANSACTION_TYPES)),
            'ticket_type' => 'required|in:appointment,walk_in',
            'walk_in_name' => 'required_if:ticket_type,walk_in|string|max:100',
            'walk_in_type' => 'required_if:ticket_type,walk_in|in:student,faculty,personnel,visitor',
        ]);

        // Generate unique queue number
        $queueNumber = QueueTicket::generateQueueNumber($validated['transaction_type']);

        // Determine priority level
        $priorityLevel = 4; // Default: Regular
        $priorityType = 'regular';

        if ($validated['ticket_type'] === 'walk_in') {
            $priorityLevel = match($validated['walk_in_type']) {
                'faculty' => 1,
                'personnel' => 2,
                'student' => 4,
                'visitor' => 4,
                default => 4
            };
            $priorityType = $validated['walk_in_type'] === 'visitor' ? 'regular' : $validated['walk_in_type'];
        } else if ($validated['patient_id']) {
            $patient = Patient::find($validated['patient_id']);
            if ($patient) {
                $priorityLevel = $patient->priority_level;
                $priorityType = match($patient->patient_type) {
                    'faculty' => 'faculty',
                    'non_academic' => 'personnel',
                    'student' => 'regular',
                    default => 'regular'
                };
            }
        }

        $ticket = QueueTicket::create([
            'patient_id' => $validated['patient_id'],
            'queue_number' => $queueNumber,
            'ticket_type' => $validated['ticket_type'],
            'transaction_type' => $validated['transaction_type'],
            'priority_level' => $priorityLevel,
            'priority_type' => $priorityType,
            'walk_in_name' => $validated['walk_in_name'] ?? null,
            'walk_in_type' => $validated['walk_in_type'] ?? null,
            'status' => 'waiting',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'ticket' => $ticket,
                'estimated_wait' => $ticket->estimated_wait,
                'position' => $ticket->position,
            ]);
        }

        return redirect()->route('queue.ticket', $ticket)
                        ->with('success', 'Queue ticket generated successfully!');
    }

    /**
     * Show ticket details and printable version
     */
    public function showTicket(QueueTicket $ticket)
    {
        return view('queue.ticket', compact('ticket'));
    }

    /**
     * Call next patient in queue
     */
    public function callNext(Request $request)
    {
        $this->authorize('update', QueueTicket::class);

        $windowNumber = $request->input('window_number', 1);

        // Get next ticket by priority
        $nextTicket = QueueTicket::waiting()
            ->byPriority()
            ->first();

        if (!$nextTicket) {
            return response()->json([
                'success' => false,
                'message' => 'No patients in queue'
            ]);
        }

        // Update ticket status
        $nextTicket->update([
            'status' => 'serving',
            'window_number' => $windowNumber,
            'called_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'ticket' => $nextTicket->load('patient'),
            'message' => "Called {$nextTicket->patient_name} to window {$windowNumber}"
        ]);
    }

    /**
     * Complete current patient service
     */
    public function completeService(QueueTicket $ticket)
    {
        $this->authorize('update', $ticket);

        $ticket->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service completed successfully'
        ]);
    }

    /**
     * Cancel/skip patient
     */
    public function cancelTicket(QueueTicket $ticket)
    {
        $this->authorize('update', $ticket);

        $ticket->update([
            'status' => 'cancelled',
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket cancelled successfully'
        ]);
    }

    /**
     * Mark patient as no-show
     */
    public function markNoShow(QueueTicket $ticket)
    {
        $this->authorize('update', $ticket);

        $ticket->update([
            'status' => 'no_show',
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Marked as no-show'
        ]);
    }

    /**
     * Get current queue status (API endpoint)
     */
    public function getQueueStatus()
    {
        $serving = QueueTicket::serving()->with('patient')->get();
        $waiting = QueueTicket::waiting()->byPriority()->with('patient')->get();

        return response()->json([
            'serving' => $serving->map(function($ticket) {
                return [
                    'id' => $ticket->id,
                    'queue_number' => $ticket->queue_number,
                    'patient_name' => $ticket->patient_name,
                    'transaction_type' => $ticket->transaction_type,
                    'window_number' => $ticket->window_number,
                    'called_at' => $ticket->called_at?->format('H:i'),
                ];
            }),
            'waiting' => $waiting->map(function($ticket) {
                return [
                    'id' => $ticket->id,
                    'queue_number' => $ticket->queue_number,
                    'patient_name' => $ticket->patient_name,
                    'transaction_type' => $ticket->transaction_type,
                    'priority_level' => $ticket->priority_level,
                    'priority_type' => $ticket->priority_type,
                    'position' => $ticket->position,
                    'estimated_wait' => $ticket->estimated_wait,
                ];
            }),
            'stats' => [
                'total_waiting' => $waiting->count(),
                'total_serving' => $serving->count(),
                'average_wait' => $this->calculateAverageWaitTime(),
            ],
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get queue statistics
     */
    public function getStats()
    {
        $today = now()->startOfDay();
        
        $stats = [
            'today' => [
                'total' => QueueTicket::where('created_at', '>=', $today)->count(),
                'completed' => QueueTicket::where('created_at', '>=', $today)->where('status', 'completed')->count(),
                'cancelled' => QueueTicket::where('created_at', '>=', $today)->where('status', 'cancelled')->count(),
                'no_show' => QueueTicket::where('created_at', '>=', $today)->where('status', 'no_show')->count(),
            ],
            'current' => [
                'waiting' => QueueTicket::waiting()->count(),
                'serving' => QueueTicket::serving()->count(),
            ],
            'by_transaction_type' => QueueTicket::where('created_at', '>=', $today)
                ->groupBy('transaction_type')
                ->selectRaw('transaction_type, count(*) as count')
                ->pluck('count', 'transaction_type'),
            'by_priority' => QueueTicket::where('created_at', '>=', $today)
                ->groupBy('priority_type')
                ->selectRaw('priority_type, count(*) as count')
                ->pluck('count', 'priority_type'),
            'hourly_distribution' => QueueTicket::where('created_at', '>=', $today)
                ->groupBy(\DB::raw('HOUR(created_at)'))
                ->selectRaw('HOUR(created_at) as hour, count(*) as count')
                ->pluck('count', 'hour'),
        ];

        return response()->json($stats);
    }

    /**
     * Calculate average wait time
     */
    private function calculateAverageWaitTime()
    {
        $completedToday = QueueTicket::today()
            ->where('status', 'completed')
            ->whereNotNull('called_at')
            ->whereNotNull('completed_at')
            ->get();

        if ($completedToday->isEmpty()) {
            return '15 minutes'; // Default estimate
        }

        $totalWaitMinutes = $completedToday->sum(function($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->called_at);
        });

        $averageMinutes = round($totalWaitMinutes / $completedToday->count());
        
        if ($averageMinutes < 60) {
            return "{$averageMinutes} minutes";
        } else {
            $hours = floor($averageMinutes / 60);
            $minutes = $averageMinutes % 60;
            return "{$hours}h {$minutes}m";
        }
    }

    /**
     * Reset daily queue (for testing/demo purposes)
     */
    public function resetQueue()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        QueueTicket::today()->delete();

        return redirect()->route('queue.management')
                        ->with('success', 'Queue reset successfully');
    }
}
