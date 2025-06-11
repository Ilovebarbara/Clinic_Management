<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\QueueTicket;
use App\Models\MedicalRecord;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Get dashboard statistics
        $stats = $this->getDashboardStats($user);

        // Redirect to appropriate dashboard based on role
        switch ($user->role) {
            case 'super_admin':
                return $this->adminDashboard($stats);
            case 'staff':
            case 'nurse':
            case 'dentist':
            case 'physician':
                return $this->staffDashboard($stats);
            default:
                return $this->defaultDashboard($stats);
        }
    }

    /**
     * Admin dashboard
     */
    private function adminDashboard($stats)
    {
        $recentUsers = User::latest()->take(5)->get();
        $recentPatients = Patient::latest()->take(5)->get();
        
        return view('dashboard.admin', compact('stats', 'recentUsers', 'recentPatients'));
    }

    /**
     * Staff dashboard
     */
    private function staffDashboard($stats)
    {
        $todayAppointments = Appointment::today()
            ->with(['patient', 'doctor'])
            ->orderBy('appointment_date')
            ->get();
            
        $currentQueue = QueueTicket::waiting()
            ->byPriority()
            ->with('patient')
            ->take(10)
            ->get();
            
        $recentRecords = MedicalRecord::latest()
            ->with(['patient', 'doctor'])
            ->take(5)
            ->get();

        return view('dashboard.staff', compact(
            'stats', 
            'todayAppointments', 
            'currentQueue', 
            'recentRecords'
        ));
    }

    /**
     * Default dashboard
     */
    private function defaultDashboard($stats)
    {
        return view('dashboard.default', compact('stats'));
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats($user)
    {
        $stats = [
            'total_patients' => Patient::count(),
            'today_appointments' => Appointment::today()->count(),
            'pending_queue' => QueueTicket::waiting()->count(),
            'completed_today' => QueueTicket::today()->where('status', 'completed')->count(),
            'total_staff' => User::where('role', '!=', 'patient')->count(),
            'active_doctors' => \App\Models\Doctor::where('is_active', true)->count(),
        ];

        // Role-specific stats
        if ($user->isStaff()) {
            $stats['monthly_patients'] = Patient::whereMonth('created_at', now()->month)->count();
            $stats['monthly_appointments'] = Appointment::whereMonth('created_at', now()->month)->count();
            $stats['monthly_records'] = MedicalRecord::whereMonth('created_at', now()->month)->count();
        }

        if ($user->isSuperAdmin()) {
            $stats['inactive_users'] = User::where('is_active', false)->count();
            $stats['system_uptime'] = $this->getSystemUptime();
        }

        return $stats;
    }

    /**
     * Get system uptime (placeholder)
     */
    private function getSystemUptime()
    {
        // This would typically involve system monitoring
        return '99.9%';
    }

    /**
     * Get real-time dashboard data (AJAX endpoint)
     */
    public function getDashboardData(Request $request)
    {
        $user = Auth::user();
        $stats = $this->getDashboardStats($user);

        // Add real-time queue information
        $currentQueue = QueueTicket::waiting()
            ->byPriority()
            ->with('patient')
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'queue_number' => $ticket->queue_number,
                    'patient_name' => $ticket->patient_name,
                    'transaction_type' => $ticket->transaction_type,
                    'priority_level' => $ticket->priority_level,
                    'priority_type' => $ticket->priority_type,
                    'estimated_wait' => $ticket->estimated_wait,
                    'position' => $ticket->position,
                ];
            });

        return response()->json([
            'stats' => $stats,
            'current_queue' => $currentQueue,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Patient portal dashboard
     */
    public function patientDashboard()
    {
        $patient = Auth::guard('patient')->user();
        
        if (!$patient) {
            return redirect()->route('patient.login');
        }

        $upcomingAppointments = $patient->appointments()
            ->upcoming()
            ->with('doctor')
            ->orderBy('appointment_date')
            ->take(5)
            ->get();

        $recentRecords = $patient->medicalRecords()
            ->latest()
            ->with('doctor')
            ->take(5)
            ->get();

        $currentTickets = $patient->queueTickets()
            ->active()
            ->latest()
            ->get();

        $patientStats = [
            'total_appointments' => $patient->appointments()->count(),
            'completed_appointments' => $patient->appointments()->where('status', 'completed')->count(),
            'total_records' => $patient->medicalRecords()->count(),
            'pending_tickets' => $patient->queueTickets()->waiting()->count(),
        ];

        return view('patient.dashboard', compact(
            'patient',
            'upcomingAppointments',
            'recentRecords',
            'currentTickets',
            'patientStats'
        ));
    }
}
