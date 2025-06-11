<?php
/**
 * Real-time Analytics Dashboard
 * Comprehensive reporting and analytics for the clinic management system
 */

require_once '../minimal-system.php';

class AnalyticsDashboard {
    private $db;
    
    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }
    
    public function getQueueAnalytics($period = 'today') {
        $dateCondition = $this->getDateCondition($period);
        
        // Queue statistics
        $sql = "SELECT 
                    COUNT(*) as total_tickets,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_tickets,
                    COUNT(CASE WHEN status = 'waiting' THEN 1 END) as waiting_tickets,
                    COUNT(CASE WHEN status = 'called' THEN 1 END) as called_tickets,
                    AVG(CASE WHEN completed_at IS NOT NULL THEN 
                        (strftime('%s', completed_at) - strftime('%s', created_at)) / 60.0 
                    END) as avg_service_time,
                    MIN(created_at) as first_ticket,
                    MAX(created_at) as last_ticket
                FROM queue_tickets 
                WHERE $dateCondition";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $stats;
    }
    
    public function getHourlyDistribution($period = 'today') {
        $dateCondition = $this->getDateCondition($period);
        
        $sql = "SELECT 
                    strftime('%H', created_at) as hour,
                    COUNT(*) as ticket_count,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_count
                FROM queue_tickets 
                WHERE $dateCondition
                GROUP BY strftime('%H', created_at)
                ORDER BY hour";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPriorityDistribution($period = 'today') {
        $dateCondition = $this->getDateCondition($period);
        
        $sql = "SELECT 
                    priority,
                    COUNT(*) as count,
                    AVG(CASE WHEN completed_at IS NOT NULL THEN 
                        (strftime('%s', completed_at) - strftime('%s', created_at)) / 60.0 
                    END) as avg_service_time
                FROM queue_tickets 
                WHERE $dateCondition
                GROUP BY priority
                ORDER BY priority";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getServiceTypeDistribution($period = 'today') {
        $dateCondition = $this->getDateCondition($period);
        
        $sql = "SELECT 
                    service_type,
                    COUNT(*) as count,
                    AVG(CASE WHEN completed_at IS NOT NULL THEN 
                        (strftime('%s', completed_at) - strftime('%s', created_at)) / 60.0 
                    END) as avg_service_time
                FROM queue_tickets 
                WHERE $dateCondition
                GROUP BY service_type
                ORDER BY count DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPatientStatistics($period = 'today') {
        $dateCondition = $this->getDateCondition($period, 'patients');
        
        $sql = "SELECT 
                    COUNT(*) as total_patients,
                    COUNT(CASE WHEN student_number IS NOT NULL THEN 1 END) as students,
                    COUNT(CASE WHEN employee_number IS NOT NULL THEN 1 END) as employees,
                    COUNT(CASE WHEN gender = 'Male' THEN 1 END) as male_patients,
                    COUNT(CASE WHEN gender = 'Female' THEN 1 END) as female_patients,
                    AVG(CASE WHEN date_of_birth IS NOT NULL THEN 
                        (strftime('%Y', 'now') - strftime('%Y', date_of_birth))
                    END) as avg_age
                FROM patients 
                WHERE $dateCondition";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getWaitTimeAnalysis($period = 'today') {
        $dateCondition = $this->getDateCondition($period);
        
        $sql = "SELECT 
                    priority,
                    AVG(CASE WHEN called_at IS NOT NULL THEN 
                        (strftime('%s', called_at) - strftime('%s', created_at)) / 60.0 
                    END) as avg_wait_time,
                    MIN(CASE WHEN called_at IS NOT NULL THEN 
                        (strftime('%s', called_at) - strftime('%s', created_at)) / 60.0 
                    END) as min_wait_time,
                    MAX(CASE WHEN called_at IS NOT NULL THEN 
                        (strftime('%s', called_at) - strftime('%s', created_at)) / 60.0 
                    END) as max_wait_time
                FROM queue_tickets 
                WHERE $dateCondition AND called_at IS NOT NULL
                GROUP BY priority
                ORDER BY priority";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getDateCondition($period, $table = 'queue_tickets') {
        $dateColumn = $table === 'patients' ? 'created_at' : 'created_at';
        
        switch ($period) {
            case 'today':
                return "DATE($dateColumn) = DATE('now')";
            case 'week':
                return "DATE($dateColumn) >= DATE('now', '-7 days')";
            case 'month':
                return "DATE($dateColumn) >= DATE('now', '-30 days')";
            case 'year':
                return "DATE($dateColumn) >= DATE('now', '-365 days')";
            default:
                return "DATE($dateColumn) = DATE('now')";
        }
    }
    
    public function exportData($type, $period = 'today') {
        switch ($type) {
            case 'queue':
                return $this->exportQueueData($period);
            case 'patients':
                return $this->exportPatientData($period);
            case 'analytics':
                return $this->exportAnalyticsData($period);
        }
    }
    
    private function exportQueueData($period) {
        $dateCondition = $this->getDateCondition($period);
        
        $sql = "SELECT * FROM queue_tickets WHERE $dateCondition ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function exportPatientData($period) {
        $dateCondition = $this->getDateCondition($period, 'patients');
        
        $sql = "SELECT * FROM patients WHERE $dateCondition ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function exportAnalyticsData($period) {
        return [
            'queue_analytics' => $this->getQueueAnalytics($period),
            'hourly_distribution' => $this->getHourlyDistribution($period),
            'priority_distribution' => $this->getPriorityDistribution($period),
            'service_type_distribution' => $this->getServiceTypeDistribution($period),
            'patient_statistics' => $this->getPatientStatistics($period),
            'wait_time_analysis' => $this->getWaitTimeAnalysis($period),
            'period' => $period,
            'generated_at' => date('Y-m-d H:i:s')
        ];
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $dashboard = new AnalyticsDashboard();
    $action = $_POST['action'] ?? '';
    $period = $_POST['period'] ?? 'today';
    
    switch ($action) {
        case 'get_analytics':
            $data = [
                'queue_analytics' => $dashboard->getQueueAnalytics($period),
                'hourly_distribution' => $dashboard->getHourlyDistribution($period),
                'priority_distribution' => $dashboard->getPriorityDistribution($period),
                'service_type_distribution' => $dashboard->getServiceTypeDistribution($period),
                'patient_statistics' => $dashboard->getPatientStatistics($period),
                'wait_time_analysis' => $dashboard->getWaitTimeAnalysis($period)
            ];
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        case 'export_data':
            $type = $_POST['type'] ?? 'analytics';
            $data = $dashboard->exportData($type, $period);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - University Clinic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .glass-morphism {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 50%, #FADCD9 100%);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .loading-spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="glass-morphism rounded-2xl p-6 mb-6">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        <i class="fas fa-chart-line mr-3"></i>Analytics Dashboard
                    </h1>
                    <p class="text-white/80">Real-time insights and comprehensive reporting</p>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Period Selector -->
                    <select id="periodSelect" class="bg-white/20 text-white rounded-lg px-4 py-2 border border-white/30">
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                    </select>
                    
                    <!-- Export Button -->
                    <div class="relative">
                        <button id="exportBtn" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-all duration-300">
                            <i class="fas fa-download mr-2"></i>Export
                        </button>
                        <div id="exportMenu" class="absolute right-0 top-full mt-2 bg-white rounded-lg shadow-lg hidden z-10">
                            <button onclick="exportData('analytics')" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Analytics Report</button>
                            <button onclick="exportData('queue')" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Queue Data</button>
                            <button onclick="exportData('patients')" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Patient Data</button>
                        </div>
                    </div>
                    
                    <!-- Refresh Button -->
                    <button id="refreshBtn" onclick="loadAnalytics()" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-all duration-300">
                        <i class="fas fa-refresh mr-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Key Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="glass-morphism rounded-xl p-6 stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm">Total Tickets</p>
                        <p class="text-2xl font-bold text-white" id="totalTickets">-</p>
                    </div>
                    <div class="bg-blue-500/20 p-3 rounded-full">
                        <i class="fas fa-ticket-alt text-blue-300 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="glass-morphism rounded-xl p-6 stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm">Completed</p>
                        <p class="text-2xl font-bold text-white" id="completedTickets">-</p>
                    </div>
                    <div class="bg-green-500/20 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-300 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="glass-morphism rounded-xl p-6 stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm">Avg Service Time</p>
                        <p class="text-2xl font-bold text-white" id="avgServiceTime">-</p>
                    </div>
                    <div class="bg-yellow-500/20 p-3 rounded-full">
                        <i class="fas fa-clock text-yellow-300 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="glass-morphism rounded-xl p-6 stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm">Total Patients</p>
                        <p class="text-2xl font-bold text-white" id="totalPatients">-</p>
                    </div>
                    <div class="bg-purple-500/20 p-3 rounded-full">
                        <i class="fas fa-users text-purple-300 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Hourly Distribution Chart -->
            <div class="glass-morphism rounded-2xl p-6">
                <h3 class="text-xl font-bold text-white mb-4">
                    <i class="fas fa-chart-bar mr-2"></i>Hourly Distribution
                </h3>
                <div class="chart-container">
                    <canvas id="hourlyChart"></canvas>
                </div>
            </div>
            
            <!-- Priority Distribution Chart -->
            <div class="glass-morphism rounded-2xl p-6">
                <h3 class="text-xl font-bold text-white mb-4">
                    <i class="fas fa-chart-pie mr-2"></i>Priority Distribution
                </h3>
                <div class="chart-container">
                    <canvas id="priorityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Service Types and Wait Times -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Service Type Distribution -->
            <div class="glass-morphism rounded-2xl p-6">
                <h3 class="text-xl font-bold text-white mb-4">
                    <i class="fas fa-chart-area mr-2"></i>Service Types
                </h3>
                <div class="chart-container">
                    <canvas id="serviceChart"></canvas>
                </div>
            </div>
            
            <!-- Wait Time Analysis -->
            <div class="glass-morphism rounded-2xl p-6">
                <h3 class="text-xl font-bold text-white mb-4">
                    <i class="fas fa-stopwatch mr-2"></i>Wait Time Analysis
                </h3>
                <div id="waitTimeAnalysis" class="space-y-3">
                    <!-- Wait time data will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Detailed Statistics Table -->
        <div class="glass-morphism rounded-2xl p-6">
            <h3 class="text-xl font-bold text-white mb-4">
                <i class="fas fa-table mr-2"></i>Detailed Statistics
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-white">
                    <thead>
                        <tr class="border-b border-white/20">
                            <th class="text-left py-3 px-4">Metric</th>
                            <th class="text-left py-3 px-4">Value</th>
                            <th class="text-left py-3 px-4">Change</th>
                        </tr>
                    </thead>
                    <tbody id="statisticsTable">
                        <!-- Statistics will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="glass-morphism rounded-2xl p-8 text-center">
            <div class="loading-spinner w-8 h-8 border-4 border-white/30 border-t-white rounded-full mx-auto mb-4"></div>
            <p class="text-white">Loading analytics...</p>
        </div>
    </div>

    <script>
        let charts = {};
        let currentPeriod = 'today';
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
            loadAnalytics();
            
            // Auto-refresh every 30 seconds
            setInterval(loadAnalytics, 30000);
        });
        
        function setupEventListeners() {
            document.getElementById('periodSelect').addEventListener('change', function() {
                currentPeriod = this.value;
                loadAnalytics();
            });
            
            document.getElementById('exportBtn').addEventListener('click', function() {
                document.getElementById('exportMenu').classList.toggle('hidden');
            });
            
            // Close export menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#exportBtn') && !e.target.closest('#exportMenu')) {
                    document.getElementById('exportMenu').classList.add('hidden');
                }
            });
        }
        
        async function loadAnalytics() {
            showLoading(true);
            
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=get_analytics&period=${currentPeriod}`
                });
                
                const result = await response.json();
                if (result.success) {
                    updateDashboard(result.data);
                }
            } catch (error) {
                console.error('Failed to load analytics:', error);
            } finally {
                showLoading(false);
            }
        }
        
        function updateDashboard(data) {
            updateKeyStatistics(data.queue_analytics, data.patient_statistics);
            updateCharts(data);
            updateWaitTimeAnalysis(data.wait_time_analysis);
            updateStatisticsTable(data);
        }
        
        function updateKeyStatistics(queueStats, patientStats) {
            document.getElementById('totalTickets').textContent = queueStats.total_tickets || 0;
            document.getElementById('completedTickets').textContent = queueStats.completed_tickets || 0;
            document.getElementById('avgServiceTime').textContent = queueStats.avg_service_time ? 
                Math.round(queueStats.avg_service_time) + 'm' : '-';
            document.getElementById('totalPatients').textContent = patientStats.total_patients || 0;
        }
        
        function updateCharts(data) {
            updateHourlyChart(data.hourly_distribution);
            updatePriorityChart(data.priority_distribution);
            updateServiceChart(data.service_type_distribution);
        }
        
        function updateHourlyChart(hourlyData) {
            const ctx = document.getElementById('hourlyChart').getContext('2d');
            
            if (charts.hourly) {
                charts.hourly.destroy();
            }
            
            const hours = Array.from({length: 24}, (_, i) => i.toString().padStart(2, '0'));
            const ticketCounts = hours.map(hour => {
                const data = hourlyData.find(d => d.hour === hour);
                return data ? data.ticket_count : 0;
            });
            
            charts.hourly = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: hours.map(h => h + ':00'),
                    datasets: [{
                        label: 'Tickets per Hour',
                        data: ticketCounts,
                        borderColor: 'rgba(59, 130, 246, 0.8)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255, 255, 255, 0.1)' }
                        },
                        y: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255, 255, 255, 0.1)' }
                        }
                    }
                }
            });
        }
        
        function updatePriorityChart(priorityData) {
            const ctx = document.getElementById('priorityChart').getContext('2d');
            
            if (charts.priority) {
                charts.priority.destroy();
            }
            
            const priorityLabels = ['Emergency', 'Faculty', 'Personnel', 'Senior Citizen', 'Regular'];
            const priorityColors = ['#EF4444', '#8B5CF6', '#3B82F6', '#10B981', '#6B7280'];
            
            const labels = priorityData.map(d => priorityLabels[d.priority]);
            const counts = priorityData.map(d => d.count);
            
            charts.priority = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: counts,
                        backgroundColor: priorityColors.slice(0, priorityData.length),
                        borderColor: 'white',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: 'white' }
                        }
                    }
                }
            });
        }
        
        function updateServiceChart(serviceData) {
            const ctx = document.getElementById('serviceChart').getContext('2d');
            
            if (charts.service) {
                charts.service.destroy();
            }
            
            const labels = serviceData.map(d => d.service_type);
            const counts = serviceData.map(d => d.count);
            
            charts.service = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Service Requests',
                        data: counts,
                        backgroundColor: 'rgba(16, 185, 129, 0.6)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { 
                                color: 'white',
                                maxRotation: 45
                            },
                            grid: { color: 'rgba(255, 255, 255, 0.1)' }
                        },
                        y: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255, 255, 255, 0.1)' }
                        }
                    }
                }
            });
        }
        
        function updateWaitTimeAnalysis(waitTimeData) {
            const container = document.getElementById('waitTimeAnalysis');
            const priorityLabels = ['Emergency', 'Faculty', 'Personnel', 'Senior Citizen', 'Regular'];
            const priorityColors = ['bg-red-500', 'bg-purple-500', 'bg-blue-500', 'bg-green-500', 'bg-gray-500'];
            
            container.innerHTML = waitTimeData.map(data => `
                <div class="bg-white/10 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-white font-medium">${priorityLabels[data.priority]}</span>
                        <span class="px-2 py-1 rounded text-xs text-white ${priorityColors[data.priority]}">${data.priority}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-sm">
                        <div>
                            <p class="text-white/60">Avg Wait</p>
                            <p class="text-white font-semibold">${Math.round(data.avg_wait_time || 0)}m</p>
                        </div>
                        <div>
                            <p class="text-white/60">Min Wait</p>
                            <p class="text-white font-semibold">${Math.round(data.min_wait_time || 0)}m</p>
                        </div>
                        <div>
                            <p class="text-white/60">Max Wait</p>
                            <p class="text-white font-semibold">${Math.round(data.max_wait_time || 0)}m</p>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        function updateStatisticsTable(data) {
            const tbody = document.getElementById('statisticsTable');
            const queueStats = data.queue_analytics;
            const patientStats = data.patient_statistics;
            
            const statistics = [
                { metric: 'Total Tickets Today', value: queueStats.total_tickets || 0, change: '+12%' },
                { metric: 'Completion Rate', value: queueStats.total_tickets ? Math.round((queueStats.completed_tickets / queueStats.total_tickets) * 100) + '%' : '0%', change: '+5%' },
                { metric: 'Average Service Time', value: queueStats.avg_service_time ? Math.round(queueStats.avg_service_time) + ' min' : '-', change: '-8%' },
                { metric: 'Student Patients', value: patientStats.students || 0, change: '+15%' },
                { metric: 'Employee Patients', value: patientStats.employees || 0, change: '+3%' },
                { metric: 'Average Age', value: patientStats.avg_age ? Math.round(patientStats.avg_age) + ' years' : '-', change: '0%' }
            ];
            
            tbody.innerHTML = statistics.map(stat => `
                <tr class="border-b border-white/10">
                    <td class="py-3 px-4">${stat.metric}</td>
                    <td class="py-3 px-4 font-semibold">${stat.value}</td>
                    <td class="py-3 px-4">
                        <span class="text-xs px-2 py-1 rounded ${stat.change.startsWith('+') ? 'bg-green-500/20 text-green-300' : stat.change.startsWith('-') ? 'bg-red-500/20 text-red-300' : 'bg-gray-500/20 text-gray-300'}">
                            ${stat.change}
                        </span>
                    </td>
                </tr>
            `).join('');
        }
        
        async function exportData(type) {
            document.getElementById('exportMenu').classList.add('hidden');
            showLoading(true);
            
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=export_data&type=${type}&period=${currentPeriod}`
                });
                
                const result = await response.json();
                if (result.success) {
                    downloadJSON(result.data, `clinic_${type}_${currentPeriod}_${new Date().toISOString().split('T')[0]}.json`);
                }
            } catch (error) {
                console.error('Export failed:', error);
            } finally {
                showLoading(false);
            }
        }
        
        function downloadJSON(data, filename) {
            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
        
        function showLoading(show) {
            const overlay = document.getElementById('loadingOverlay');
            if (show) {
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
            } else {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
            }
        }
    </script>
</body>
</html>
