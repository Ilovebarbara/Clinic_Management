<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simulate admin authentication
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_name'] = 'System Administrator';
    $_SESSION['admin_role'] = 'Super Admin';
}

// Sample admin dashboard data
$admin_data = [
    'system_stats' => [
        'total_users' => 45,
        'active_staff' => 12,
        'registered_patients' => 1247,
        'system_uptime' => '99.8%'
    ],
    'recent_activities' => [
        ['user' => 'Dr. Smith', 'action' => 'Created patient record', 'time' => '10 minutes ago'],
        ['user' => 'Nurse Johnson', 'action' => 'Updated appointment status', 'time' => '15 minutes ago'],
        ['user' => 'Admin', 'action' => 'System backup completed', 'time' => '1 hour ago'],
        ['user' => 'Dr. Brown', 'action' => 'Generated medical certificate', 'time' => '2 hours ago']
    ],
    'staff_list' => [
        ['name' => 'Dr. Sarah Smith', 'role' => 'Doctor', 'status' => 'Active', 'last_login' => '2 hours ago'],
        ['name' => 'Dr. Michael Johnson', 'role' => 'Doctor', 'status' => 'Active', 'last_login' => '30 minutes ago'],
        ['name' => 'Emily Brown', 'role' => 'Nurse', 'status' => 'Active', 'last_login' => '1 hour ago'],
        ['name' => 'James Wilson', 'role' => 'Receptionist', 'status' => 'Offline', 'last_login' => '1 day ago']
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo bin2hex(random_bytes(32)); ?>">
    <title>Admin Panel - University Clinic</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #F9F1F0 0%, #FADCD9 100%);
            height: 100vh;
            overflow: hidden;
        }

        /* Top Navigation */
        .top-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(247, 148, 137, 0.2);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .nav-brand i {
            font-size: 1.8rem;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #333;
        }

        .nav-subtitle {
            font-size: 0.9rem;
            color: #F79489;
            font-weight: 500;
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .user-details {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        .user-role {
            color: #F79489;
            font-size: 0.8rem;
            font-weight: 500;
        }        /* Main Layout */
        .admin-layout {
            display: grid;
            grid-template-columns: 300px 1fr;
            height: calc(100vh - 80px);
            overflow: hidden;
        }        /* Sidebar */
        .admin-sidebar {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(247, 148, 137, 0.2);
            padding: 2rem 0;
            height: 100%;
            overflow-y: auto;
        }

        .sidebar-section {
            margin-bottom: 2rem;
        }

        .sidebar-title {
            padding: 0 2rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 2rem;
            color: #666;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(247, 148, 137, 0.1);
            color: #F79489;
            border-left-color: #F79489;
        }

        .nav-icon {
            width: 20px;
            text-align: center;
        }        /* Main Content */
        .admin-content {
            padding: 2rem;
            overflow-y: auto;
            height: 100%;
        }

        .content-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .page-description {
            color: #666;
            font-size: 1.1rem;
        }

        /* Statistics Dashboard */
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #666;
            font-weight: 500;
            font-size: 1rem;
        }

        .stat-trend {
            font-size: 0.85rem;
            color: #10b981;
            font-weight: 500;
        }

        /* Content Sections */
        .content-sections {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .content-panel {
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 2rem;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .panel-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .panel-title i {
            color: #F79489;
        }

        .panel-action {
            color: #F79489;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .panel-action:hover {
            background: rgba(247, 148, 137, 0.1);
        }

        /* Staff List */
        .staff-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .staff-item {
            background: rgba(255, 255, 255, 0.6);
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .staff-item:hover {
            background: rgba(255, 255, 255, 0.8);
        }

        .staff-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .staff-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .staff-details h4 {
            color: #333;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .staff-details p {
            color: #666;
            font-size: 0.9rem;
        }

        .staff-status {
            text-align: right;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-offline {
            background: #fee2e2;
            color: #dc2626;
        }

        .last-login {
            color: #666;
            font-size: 0.8rem;
        }

        /* Activity Log */
        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .activity-item {
            background: rgba(255, 255, 255, 0.6);
            border-radius: 12px;
            padding: 1rem;
            border-left: 4px solid #F79489;
        }

        .activity-item h5 {
            color: #333;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .activity-item p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .activity-time {
            color: #F79489;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            color: white;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.5);
            color: #666;
            border: 1px solid rgba(247, 148, 137, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                display: none;
            }

            .content-sections {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .top-nav {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .admin-content {
                padding: 1rem;
            }

            .stats-overview {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-nav">
        <div class="nav-brand">
            <i class="fas fa-heartbeat"></i>
            <div>
                <div class="nav-title">University Clinic</div>
                <div class="nav-subtitle">Administration Panel</div>
            </div>
        </div>

        <div class="nav-user">
            <div class="user-details">
                <div class="user-name"><?php echo $_SESSION['admin_name']; ?></div>
                <div class="user-role"><?php echo $_SESSION['admin_role']; ?></div>
            </div>            <div class="user-avatar">SA</div>
            <a href="/logout" style="color: #F79489; font-size: 1.2rem; margin-left: 1rem;">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </nav>

    <!-- Main Layout -->
    <div class="admin-layout">
        <!-- Sidebar -->
        <div class="admin-sidebar">            <div class="sidebar-section">
                <div class="sidebar-title">Management</div>
                <nav class="sidebar-nav">
                    <a href="#dashboard" class="nav-link active">
                        <i class="fas fa-chart-pie nav-icon"></i>
                        Dashboard
                    </a>                    <a href="/admin/users" class="nav-link">
                        <i class="fas fa-users-cog nav-icon"></i>
                        User Management
                    </a>
                    <a href="/patients" class="nav-link">
                        <i class="fas fa-users nav-icon"></i>
                        Patient Records
                    </a>
                    <a href="/appointments" class="nav-link">
                        <i class="fas fa-calendar-check nav-icon"></i>
                        Appointments
                    </a>
                </nav>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-title">System</div>
                <nav class="sidebar-nav">
                    <a href="/queue" class="nav-link">
                        <i class="fas fa-list-ol nav-icon"></i>
                        Queue Management
                    </a>
                    <a href="#reports" class="nav-link">
                        <i class="fas fa-chart-bar nav-icon"></i>
                        Reports & Analytics
                    </a>
                    <a href="#settings" class="nav-link">
                        <i class="fas fa-cog nav-icon"></i>
                        System Settings
                    </a>
                    <a href="#logs" class="nav-link">
                        <i class="fas fa-list-alt nav-icon"></i>
                        System Logs
                    </a>
                </nav>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-title">Tools</div>
                <nav class="sidebar-nav">
                    <a href="#maintenance" class="nav-link">
                        <i class="fas fa-tools nav-icon"></i>
                        Maintenance
                    </a>
                    <a href="#notifications" class="nav-link">
                        <i class="fas fa-bell nav-icon"></i>
                        Notifications
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="admin-content">
            <!-- Content Header -->
            <div class="content-header">
                <h1 class="page-title">System Dashboard</h1>
                <p class="page-description">Monitor and manage your clinic management system</p>
            </div>

            <!-- Statistics Overview -->
            <div class="stats-overview">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-trend">+12% this month</div>
                    </div>
                    <div class="stat-value"><?php echo $admin_data['system_stats']['total_users']; ?></div>
                    <div class="stat-label">Total System Users</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div class="stat-trend">Currently online</div>
                    </div>
                    <div class="stat-value"><?php echo $admin_data['system_stats']['active_staff']; ?></div>
                    <div class="stat-label">Active Staff Members</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="stat-trend">+45 new this week</div>
                    </div>
                    <div class="stat-value"><?php echo number_format($admin_data['system_stats']['registered_patients']); ?></div>
                    <div class="stat-label">Registered Patients</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-server"></i>
                        </div>
                        <div class="stat-trend">Excellent</div>
                    </div>
                    <div class="stat-value"><?php echo $admin_data['system_stats']['system_uptime']; ?></div>
                    <div class="stat-label">System Uptime</div>
                </div>
            </div>

            <!-- Content Sections -->
            <div class="content-sections">
                <!-- Staff Management -->
                <div class="content-panel">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <i class="fas fa-users-cog"></i>
                            Staff Management
                        </h3>
                        <a href="#" class="panel-action" onclick="addNewStaff()">Add New Staff</a>
                    </div>

                    <div class="staff-list">
                        <?php foreach ($admin_data['staff_list'] as $staff): ?>
                        <div class="staff-item">
                            <div class="staff-info">
                                <div class="staff-avatar"><?php echo substr($staff['name'], 0, 2); ?></div>
                                <div class="staff-details">
                                    <h4><?php echo $staff['name']; ?></h4>
                                    <p><?php echo $staff['role']; ?></p>
                                </div>
                            </div>
                            <div class="staff-status">
                                <div class="status-badge status-<?php echo strtolower($staff['status']); ?>">
                                    <?php echo $staff['status']; ?>
                                </div>
                                <div class="last-login">Last login: <?php echo $staff['last_login']; ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="action-buttons">
                        <button class="btn btn-primary" onclick="manageStaff()">
                            <i class="fas fa-cog"></i>
                            Manage All Staff
                        </button>
                        <button class="btn btn-secondary" onclick="exportStaffData()">
                            <i class="fas fa-download"></i>
                            Export Data
                        </button>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="content-panel">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <i class="fas fa-history"></i>
                            Recent Activity
                        </h3>
                        <a href="#" class="panel-action" onclick="viewAllLogs()">View All Logs</a>
                    </div>

                    <div class="activity-list">
                        <?php foreach ($admin_data['recent_activities'] as $activity): ?>
                        <div class="activity-item">
                            <h5><?php echo $activity['user']; ?></h5>
                            <p><?php echo $activity['action']; ?></p>
                            <div class="activity-time"><?php echo $activity['time']; ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="action-buttons">
                        <button class="btn btn-primary" onclick="systemMaintenance()">
                            <i class="fas fa-tools"></i>
                            System Maintenance
                        </button>
                        <button class="btn btn-secondary" onclick="createBackup()">
                            <i class="fas fa-database"></i>
                            Create Backup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>    <script>
        // Admin functions - Connect to real backend        function addNewStaff() {
            // Redirect to actual user creation route
            window.location.href = '/admin/users';
        }

        function manageStaff() {
            // Redirect to actual user management route
            window.location.href = '/admin/users';
        }function exportStaffData() {
            // Create CSV export functionality
            fetch('/admin/staff/export', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
                .then(response => {
                    if (response.ok) {
                        return response.blob();
                    }
                    throw new Error('Export failed');
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = 'staff_data_' + new Date().toISOString().slice(0,10) + '.csv';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    alert('Staff data exported successfully!');
                })
                .catch(error => {
                    alert('Export failed: ' + error.message);
                });
        }

        function viewAllLogs() {
            // Open system logs in new tab
            window.open('/admin/logs', '_blank');
        }

        function systemMaintenance() {
            if (confirm('Run System Maintenance?\n\nThis will:\n• Clear temporary files\n• Optimize database\n• Update system cache\n\nProceed?')) {
                fetch('/admin/maintenance', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert('Maintenance Complete:\n' + data.message);
                })
                .catch(error => {
                    alert('Maintenance failed: ' + error.message);
                });
            }
        }

        function createBackup() {
            if (confirm('Create System Backup?\n\nThis will create a complete backup of:\n• Database\n• User files\n• System configuration\n\nProceed?')) {
                fetch('/admin/backup', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert('Backup Started\n\nBackup ID: ' + data.backup_id + '\nYou will be notified when complete.');
                })
                .catch(error => {
                    alert('Backup failed: ' + error.message);
                });
            }
        }        // Navigation highlighting
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Load real-time data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadRealTimeData();
            
            // Auto-refresh data every 30 seconds
            setInterval(loadRealTimeData, 30000);
        });

        // Load real-time system data
        function loadRealTimeData() {
            // Load current statistics from database
            fetch('/api/admin/analytics', {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                updateDashboardStats(data);
            })
            .catch(error => {
                console.log('Failed to load real-time data:', error);
            });
        }

        // Update dashboard statistics
        function updateDashboardStats(data) {
            if (data.system_usage) {
                // Update system stats if available
                console.log('Real-time data loaded:', data);
            }
        }

        // Auto-refresh system stats
        setInterval(() => {
            console.log('Refreshing system statistics...');
            loadRealTimeData();
        }, 60000);
    </script>

    <?php include 'quick-nav.php'; ?>
</body>
</html>