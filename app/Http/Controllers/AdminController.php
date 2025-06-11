<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\QueueTicket;
use App\Models\MedicalRecord;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'superadmin']);
    }

    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'active_staff' => User::where('is_active', true)->where('role', '!=', 'patient')->count(),
            'registered_patients' => Patient::count(),
            'system_uptime' => $this->getSystemUptime(),
            'total_appointments' => Appointment::count(),
            'today_appointments' => Appointment::whereDate('appointment_date', today())->count(),
            'waiting_queue' => QueueTicket::where('status', 'waiting')->count(),
            'completed_today' => QueueTicket::whereDate('created_at', today())->where('status', 'completed')->count()
        ];

        $recent_activities = $this->getRecentActivities();
        $staff_list = $this->getStaffList();

        return view('admin.dashboard', compact('stats', 'recent_activities', 'staff_list'));
    }

    /**
     * Export staff data as CSV
     */
    public function exportStaffData()
    {
        $users = User::where('role', '!=', 'patient')->get();

        $csvData = [];
        $csvData[] = ['Name', 'Username', 'Email', 'Role', 'Phone', 'Employee ID', 'Status', 'Created At', 'Last Login'];

        foreach ($users as $user) {
            $csvData[] = [
                $user->name,
                $user->username,
                $user->email,
                ucfirst($user->role),
                $user->phone ?? 'N/A',
                $user->employee_id ?? 'N/A',
                $user->is_active ? 'Active' : 'Inactive',
                $user->created_at->format('Y-m-d H:i:s'),
                $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never'
            ];
        }

        $filename = 'staff_export_' . date('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);
        exit;
    }

    /**
     * Perform system maintenance
     */
    public function systemMaintenance()
    {
        try {
            // Clear application cache
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            // Optimize database (clean up old sessions, logs, etc.)
            DB::table('sessions')->where('last_activity', '<', now()->subDays(30)->timestamp)->delete();
            
            // Clear old temporary files
            Storage::disk('local')->deleteDirectory('temp');
            Storage::disk('local')->makeDirectory('temp');

            $message = 'System maintenance completed successfully. Cache cleared, database optimized, and temporary files cleaned.';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Maintenance failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create system backup
     */
    public function createBackup()
    {
        try {
            $backupId = 'backup_' . date('Y-m-d_H-i-s');
            $backupPath = storage_path('app/backups/' . $backupId);

            // Create backup directory
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            // Backup database
            $databasePath = database_path('clinic.sqlite');
            if (file_exists($databasePath)) {
                copy($databasePath, $backupPath . '/database_backup.sqlite');
            }

            // Backup uploads/files
            $uploadsPath = storage_path('app/public');
            if (is_dir($uploadsPath)) {
                $this->copyDirectory($uploadsPath, $backupPath . '/uploads');
            }

            // Create backup info file
            $backupInfo = [
                'backup_id' => $backupId,
                'created_at' => now()->toISOString(),
                'created_by' => Auth::user()->name,
                'database_size' => file_exists($databasePath) ? filesize($databasePath) : 0,
                'total_users' => User::count(),
                'total_patients' => Patient::count(),
                'total_appointments' => Appointment::count()
            ];

            file_put_contents($backupPath . '/backup_info.json', json_encode($backupInfo, JSON_PRETTY_PRINT));

            return response()->json([
                'success' => true,
                'backup_id' => $backupId,
                'message' => 'Backup created successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system logs
     */
    public function getLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!file_exists($logFile)) {
            return response()->json([
                'logs' => [],
                'message' => 'No log file found'
            ]);
        }

        $logs = [];
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        // Get last 100 lines
        $recentLines = array_slice($lines, -100);
        
        foreach ($recentLines as $line) {
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+)$/', $line, $matches)) {
                $logs[] = [
                    'timestamp' => $matches[1],
                    'environment' => $matches[2],
                    'level' => $matches[3],
                    'message' => $matches[4]
                ];
            }
        }

        return response()->json([
            'logs' => array_reverse($logs),
            'total_lines' => count($lines)
        ]);
    }

    /**
     * Get system analytics data
     */
    public function getAnalytics()
    {
        $analytics = [
            'user_growth' => $this->getUserGrowthData(),
            'appointment_trends' => $this->getAppointmentTrends(),
            'queue_performance' => $this->getQueuePerformance(),
            'system_usage' => $this->getSystemUsage()
        ];

        return response()->json($analytics);
    }

    /**
     * Private helper methods
     */
    private function getSystemUptime()
    {
        // Simple uptime calculation based on app start
        $uptimeFile = storage_path('app/uptime_start.txt');
        
        if (!file_exists($uptimeFile)) {
            file_put_contents($uptimeFile, time());
        }
        
        $startTime = (int)file_get_contents($uptimeFile);
        $uptime = time() - $startTime;
        
        // Calculate uptime percentage (assuming 99.9% for demo)
        return '99.9%';
    }

    private function getRecentActivities()
    {
        // Get recent user activities
        $activities = [];
        
        // Recent user logins
        $recentLogins = User::whereNotNull('last_login_at')
            ->orderBy('last_login_at', 'desc')
            ->take(5)
            ->get();
            
        foreach ($recentLogins as $user) {
            $activities[] = [
                'user' => $user->name,
                'action' => 'Logged in to system',
                'time' => $user->last_login_at->diffForHumans()
            ];
        }

        // Recent patient registrations
        $recentPatients = Patient::orderBy('created_at', 'desc')->take(3)->get();
        foreach ($recentPatients as $patient) {
            $activities[] = [
                'user' => 'System',
                'action' => 'New patient registered: ' . $patient->full_name,
                'time' => $patient->created_at->diffForHumans()
            ];
        }

        // Sort by time and limit
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activities, 0, 10);
    }

    private function getStaffList()
    {
        return User::where('role', '!=', 'patient')
            ->select('id', 'name', 'role', 'is_active', 'last_login_at')
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => ucfirst($user->role),
                    'status' => $user->is_active ? 'Active' : 'Offline',
                    'last_login' => $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never'
                ];
            })
            ->toArray();
    }

    private function getUserGrowthData()
    {
        $months = [];
        $userCounts = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $userCounts[] = User::whereYear('created_at', $date->year)
                                ->whereMonth('created_at', $date->month)
                                ->count();
        }
        
        return [
            'labels' => $months,
            'data' => $userCounts
        ];
    }

    private function getAppointmentTrends()
    {
        $days = [];
        $counts = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('M j');
            $counts[] = Appointment::whereDate('appointment_date', $date)->count();
        }
        
        return [
            'labels' => $days,
            'data' => $counts
        ];
    }

    private function getQueuePerformance()
    {
        $avgWaitTime = QueueTicket::where('status', 'completed')
            ->whereDate('created_at', today())
            ->avg(DB::raw('TIMESTAMPDIFF(MINUTE, created_at, updated_at)'));
            
        return [
            'average_wait_time' => round($avgWaitTime ?? 0, 1),
            'total_served_today' => QueueTicket::whereDate('created_at', today())->where('status', 'completed')->count(),
            'current_waiting' => QueueTicket::where('status', 'waiting')->count()
        ];
    }

    private function getSystemUsage()
    {
        return [
            'disk_usage' => [
                'used' => $this->getDirectorySize(storage_path()),
                'total' => disk_total_space(storage_path())
            ],
            'database_size' => file_exists(database_path('clinic.sqlite')) ? filesize(database_path('clinic.sqlite')) : 0,
            'active_sessions' => DB::table('sessions')->count()
        ];
    }

    private function getDirectorySize($directory)
    {
        $size = 0;
        if (is_dir($directory)) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file) {
                $size += $file->getSize();
            }
        }
        return $size;
    }

    private function copyDirectory($source, $destination)
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }
}
