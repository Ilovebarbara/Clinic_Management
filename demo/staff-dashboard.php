<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simulate authentication check
if (!isset($_SESSION['staff_id'])) {
    $_SESSION['staff_id'] = 1; // Demo purposes
    $_SESSION['staff_name'] = 'Dr. Sarah Johnson';
    $_SESSION['staff_role'] = 'Doctor';
}

// Sample dashboard data
$dashboard_data = [
    'stats' => [
        'total_patients' => 156,
        'today_appointments' => 23,
        'waiting_queue' => 8,
        'completed_today' => 15
    ],
    'recent_appointments' => [
        [
            'id' => 1,
            'time' => '09:00',
            'patient' => 'John Doe',
            'patient_id' => 'STU-2023-001',
            'purpose' => 'General Consultation',
            'status' => 'Completed'
        ],
        [
            'id' => 2,
            'time' => '09:30',
            'patient' => 'Jane Smith',
            'patient_id' => 'FAC-2023-002',
            'purpose' => 'Medical Certificate',
            'status' => 'In Progress'
        ],
        [
            'id' => 3,
            'time' => '10:00',
            'patient' => 'Mike Wilson',
            'patient_id' => 'STF-2023-003',
            'purpose' => 'Follow-up',
            'status' => 'Waiting'
        ]
    ],
    'queue_status' => [
        ['number' => 'Q087', 'patient' => 'Alice Brown', 'service' => 'Consultation', 'wait_time' => '5 min'],
        ['number' => 'Q088', 'patient' => 'Bob Johnson', 'service' => 'Certificate', 'wait_time' => '12 min'],
        ['number' => 'Q089', 'patient' => 'Carol Davis', 'service' => 'Check-up', 'wait_time' => '18 min']
    ]
};
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - University Clinic</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #F9F1F0 0%, #FADCD9 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(247, 148, 137, 0.2);
            z-index: 100;
            padding: 2rem 0;
        }

        .sidebar-header {
            padding: 0 2rem 2rem;
            border-bottom: 1px solid rgba(247, 148, 137, 0.1);
        }

        .clinic-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .clinic-logo i {
            font-size: 2rem;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .clinic-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
        }

        .staff-info {
            background: rgba(247, 148, 137, 0.1);
            padding: 1rem;
            border-radius: 12px;
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
            margin-bottom: 0.75rem;
        }

        .staff-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
        }

        .staff-role {
            color: #F79489;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .sidebar-nav {
            padding: 2rem 0;
        }

        .nav-item {
            display: block;
            padding: 0.75rem 2rem;
            color: #666;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-item:hover,
        .nav-item.active {
            background: rgba(247, 148, 137, 0.1);
            color: #F79489;
            border-left-color: #F79489;
        }

        .nav-item i {
            width: 20px;
            margin-right: 0.75rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            min-height: 100vh;
        }

        .content-header {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: #666;
            font-size: 1.1rem;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
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
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        /* Statistics Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #666;
            font-weight: 500;
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .content-section {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 2rem;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            color: #F79489;
        }

        /* Appointment List */
        .appointment-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .appointment-item {
            background: rgba(255, 255, 255, 0.6);
            border-radius: 12px;
            padding: 1.25rem;
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 1rem;
            align-items: center;
            transition: all 0.3s ease;
        }

        .appointment-item:hover {
            background: rgba(255, 255, 255, 0.8);
            transform: translateX(5px);
        }

        .appointment-time {
            background: #F79489;
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .appointment-details h4 {
            color: #333;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .appointment-details p {
            color: #666;
            font-size: 0.9rem;
        }

        .appointment-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-completed { background: #dcfce7; color: #166534; }
        .status-in-progress { background: #fef3c7; color: #92400e; }
        .status-waiting { background: #dbeafe; color: #1d4ed8; }

        /* Queue Status */
        .queue-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .queue-item {
            background: rgba(255, 255, 255, 0.6);
            border-radius: 12px;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .queue-number {
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .queue-details {
            flex: 1;
            margin-left: 1rem;
        }

        .queue-details h5 {
            color: #333;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .queue-details p {
            color: #666;
            font-size: 0.85rem;
        }

        .queue-time {
            color: #F79489;
            font-weight: 500;
            font-size: 0.85rem;
        }

        /* Quick Actions */
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quick-action-card {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .quick-action-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.4);
        }

        .action-icon-small {
            width: 50px;
            height: 50px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .action-title {
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }

            .content-header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .appointment-item {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="clinic-logo">
                <i class="fas fa-heartbeat"></i>
                <span class="clinic-name">University Clinic</span>
            </div>
            
            <div class="staff-info">
                <div class="staff-avatar">SJ</div>
                <div class="staff-name"><?php echo $_SESSION['staff_name']; ?></div>
                <div class="staff-role"><?php echo $_SESSION['staff_role']; ?></div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="#dashboard" class="nav-item active">
                <i class="fas fa-chart-line"></i>
                Dashboard
            </a>
            <a href="patients.php" class="nav-item">
                <i class="fas fa-users"></i>
                Patients
            </a>
            <a href="appointments.php" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                Appointments
            </a>
            <a href="queue.php" class="nav-item">
                <i class="fas fa-list-ol"></i>
                Queue Management
            </a>
            <a href="medical-records.php" class="nav-item">
                <i class="fas fa-file-medical"></i>
                Medical Records
            </a>
            <a href="#reports" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                Reports
            </a>
            <a href="#settings" class="nav-item">
                <i class="fas fa-cog"></i>
                Settings
            </a>            <a href="/logout" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Content Header -->
        <div class="content-header">
            <div>
                <h1 class="page-title">Staff Dashboard</h1>
                <p class="page-subtitle">Welcome back, <?php echo explode(' ', $_SESSION['staff_name'])[1]; ?>! Here's what's happening today.</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="callNextPatient()">
                    <i class="fas fa-volume-up"></i>
                    Call Next
                </button>
                <button class="btn btn-primary" onclick="addNewPatient()">
                    <i class="fas fa-user-plus"></i>
                    New Patient
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?php echo $dashboard_data['stats']['total_patients']; ?></div>
                <div class="stat-label">Total Patients</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-number"><?php echo $dashboard_data['stats']['today_appointments']; ?></div>
                <div class="stat-label">Today's Appointments</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number"><?php echo $dashboard_data['stats']['waiting_queue']; ?></div>
                <div class="stat-label">Patients Waiting</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number"><?php echo $dashboard_data['stats']['completed_today']; ?></div>
                <div class="stat-label">Completed Today</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions-grid">
            <div class="quick-action-card" onclick="viewPatients()">
                <div class="action-icon-small">
                    <i class="fas fa-search"></i>
                </div>
                <div class="action-title">Search Patients</div>
            </div>

            <div class="quick-action-card" onclick="addAppointment()">
                <div class="action-icon-small">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <div class="action-title">Book Appointment</div>
            </div>

            <div class="quick-action-card" onclick="generateReport()">
                <div class="action-icon-small">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="action-title">Generate Report</div>
            </div>

            <div class="quick-action-card" onclick="manageQueue()">
                <div class="action-icon-small">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="action-title">Manage Queue</div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Recent Appointments -->
            <div class="content-section">
                <h2 class="section-title">
                    <i class="fas fa-calendar-alt"></i>
                    Today's Schedule
                </h2>

                <div class="appointment-list">
                    <?php foreach ($dashboard_data['recent_appointments'] as $appointment): ?>
                    <div class="appointment-item">
                        <div class="appointment-time"><?php echo $appointment['time']; ?></div>
                        <div class="appointment-details">
                            <h4><?php echo $appointment['patient']; ?></h4>
                            <p><?php echo $appointment['patient_id']; ?> • <?php echo $appointment['purpose']; ?></p>
                        </div>
                        <div class="appointment-status status-<?php echo strtolower(str_replace(' ', '-', $appointment['status'])); ?>">
                            <?php echo $appointment['status']; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <button class="btn btn-secondary" onclick="viewAllAppointments()" style="width: 100%; margin-top: 1rem;">
                    View All Appointments
                </button>
            </div>

            <!-- Queue Status -->
            <div class="content-section">
                <h2 class="section-title">
                    <i class="fas fa-list-ol"></i>
                    Current Queue
                </h2>

                <div class="queue-list">
                    <?php foreach ($dashboard_data['queue_status'] as $queue): ?>
                    <div class="queue-item">
                        <div class="queue-number"><?php echo $queue['number']; ?></div>
                        <div class="queue-details">
                            <h5><?php echo $queue['patient']; ?></h5>
                            <p><?php echo $queue['service']; ?></p>
                        </div>
                        <div class="queue-time"><?php echo $queue['wait_time']; ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <button class="btn btn-primary" onclick="manageQueue()" style="width: 100%; margin-top: 1rem;">
                    Manage Queue
                </button>
            </div>
        </div>
    </div>

    <?php include 'quick-nav.php'; ?>

    <script>
        // Quick action functions
        function callNextPatient() {
            alert('Calling next patient in queue...\n\nQ087 - Alice Brown\nPlease proceed to consultation room.');
        }

        function addNewPatient() {
            alert('Add New Patient\n\nOpening patient registration form...');
        }

        function viewPatients() {
            window.location.href = 'patients.php';
        }

        function addAppointment() {
            window.location.href = 'appointments.php';
        }

        function generateReport() {
            alert('Generate Report\n\nAvailable reports:\n• Daily Summary\n• Patient Statistics\n• Appointment Analysis\n• Queue Performance');
        }

        function manageQueue() {
            window.location.href = 'queue.php';
        }

        function viewAllAppointments() {
            window.location.href = 'appointments.php';
        }

        // Mobile sidebar toggle
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
        }

        // Add mobile menu button for small screens
        if (window.innerWidth <= 1024) {
            const header = document.querySelector('.content-header');
            const menuButton = document.createElement('button');
            menuButton.innerHTML = '<i class="fas fa-bars"></i>';
            menuButton.className = 'btn btn-secondary';
            menuButton.onclick = toggleSidebar;
            header.querySelector('.header-actions').prepend(menuButton);
        }

        // Auto-refresh dashboard data
        setInterval(() => {
            // In a real application, this would fetch updated data
            console.log('Refreshing dashboard data...');        }, 30000);
    </script>

    <?php include 'quick-nav.php'; ?>
</body>
</html>
