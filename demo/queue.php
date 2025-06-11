<?php
session_start();

// Simulate authentication check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Sample queue data
$queue_tickets = [
    [
        'id' => 1,
        'ticket_number' => 'Q001',
        'patient_name' => 'John Doe',
        'patient_id' => 'STU-2023-001',
        'service_type' => 'General Consultation',
        'priority' => 4,
        'priority_label' => 'Regular',
        'status' => 'Waiting',
        'window_number' => null,
        'created_at' => '2024-06-11 08:30:00',
        'estimated_wait' => '15 minutes',
        'phone' => '+1234567890'
    ],
    [
        'id' => 2,
        'ticket_number' => 'Q002',
        'patient_name' => 'Prof. Jane Smith',
        'patient_id' => 'FAC-2023-002',
        'service_type' => 'Medical Certificate',
        'priority' => 1,
        'priority_label' => 'Faculty',
        'status' => 'Being Served',
        'window_number' => 1,
        'created_at' => '2024-06-11 08:45:00',
        'estimated_wait' => '0 minutes',
        'phone' => '+1234567892'
    ],
    [
        'id' => 3,
        'ticket_number' => 'Q003',
        'patient_name' => 'Mike Wilson',
        'patient_id' => 'STF-2023-003',
        'service_type' => 'Prescription Refill',
        'priority' => 2,
        'priority_label' => 'Personnel',
        'status' => 'Called',
        'window_number' => 2,
        'created_at' => '2024-06-11 09:00:00',
        'estimated_wait' => '5 minutes',
        'phone' => '+1234567894'
    ],
    [
        'id' => 4,
        'ticket_number' => 'Q004',
        'patient_name' => 'Sarah Davis',
        'patient_id' => 'STU-2023-004',
        'service_type' => 'Health Screening',
        'priority' => 4,
        'priority_label' => 'Regular',
        'status' => 'Waiting',
        'window_number' => null,
        'created_at' => '2024-06-11 09:15:00',
        'estimated_wait' => '25 minutes',
        'phone' => '+1234567896'
    ],
    [
        'id' => 5,
        'ticket_number' => 'Q005',
        'patient_name' => 'Dr. Tom Anderson',
        'patient_id' => 'FAC-2023-005',
        'service_type' => 'Emergency Consultation',
        'priority' => 0,
        'priority_label' => 'Emergency',
        'status' => 'Waiting',
        'window_number' => null,
        'created_at' => '2024-06-11 09:20:00',
        'estimated_wait' => '2 minutes',
        'phone' => '+1234567898'
    ]
];

$windows = [
    ['id' => 1, 'name' => 'Window 1', 'status' => 'Busy', 'current_ticket' => 'Q002'],
    ['id' => 2, 'name' => 'Window 2', 'status' => 'Available', 'current_ticket' => null],
    ['id' => 3, 'name' => 'Window 3', 'status' => 'Available', 'current_ticket' => null],
    ['id' => 4, 'name' => 'Emergency', 'status' => 'Available', 'current_ticket' => null]
];

$priority_colors = [
    0 => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-exclamation-triangle'],
    1 => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'icon' => 'fa-crown'],
    2 => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-briefcase'],
    3 => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-graduation-cap'],
    4 => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-user']
];

$status_colors = [
    'Waiting' => 'bg-yellow-100 text-yellow-800',
    'Called' => 'bg-blue-100 text-blue-800',
    'Being Served' => 'bg-green-100 text-green-800',
    'Completed' => 'bg-gray-100 text-gray-800',
    'No Show' => 'bg-red-100 text-red-800'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Management - University Clinic</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

        /* Floating Background Orbs */
        .floating-orb {
            position: fixed;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(247, 148, 137, 0.1) 0%, rgba(248, 175, 166, 0.15) 100%);
            animation: floatOrb 20s infinite ease-in-out;
            pointer-events: none;
            z-index: 0;
        }

        .floating-orb:nth-child(1) {
            width: 300px;
            height: 300px;
            top: -100px;
            right: -100px;
            animation-delay: 0s;
        }

        .floating-orb:nth-child(2) {
            width: 200px;
            height: 200px;
            bottom: -50px;
            left: -50px;
            animation-delay: -7s;
        }

        .floating-orb:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 50%;
            left: 10%;
            animation-delay: -14s;
        }

        @keyframes floatOrb {
            0%, 100% { transform: translate(0, 0) rotate(0deg) scale(1); }
            25% { transform: translate(30px, -30px) rotate(90deg) scale(1.1); }
            50% { transform: translate(-20px, 20px) rotate(180deg) scale(0.9); }
            75% { transform: translate(-30px, -10px) rotate(270deg) scale(1.05); }
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            z-index: 1000;
            padding: 1rem 2rem;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .logo i {
            font-size: 1.5rem;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo span {
            font-weight: 700;
            font-size: 1.25rem;
            background: linear-gradient(135deg, #333 0%, #666 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-menu {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-menu a {
            text-decoration: none;
            color: #666;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-menu a.active,
        .nav-menu a:hover {
            color: #F79489;
        }

        .nav-menu a.active::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            border-radius: 1px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            margin-top: 100px;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header {
            margin-bottom: 2rem;
            animation: slideInUp 0.8s ease;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #333 0%, #666 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: #666;
            font-size: 1.1rem;
        }

        /* Queue Overview */
        .queue-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
            animation: slideInUp 0.8s ease 0.2s both;
        }

        .overview-card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .overview-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .overview-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .overview-number {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .overview-label {
            color: #666;
            font-weight: 500;
        }

        /* Windows Status */
        .windows-section {
            margin-bottom: 2rem;
            animation: slideInUp 0.8s ease 0.4s both;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }

        .windows-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .window-card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            padding: 1.25rem;
            transition: all 0.3s ease;
        }

        .window-card.busy {
            border-color: #F79489;
            background: rgba(247, 148, 137, 0.1);
        }

        .window-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .window-name {
            font-weight: 600;
            color: #333;
        }

        .window-status {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-available {
            background: #dcfce7;
            color: #166534;
        }

        .status-busy {
            background: #fee2e2;
            color: #dc2626;
        }

        .current-ticket {
            font-size: 1.1rem;
            font-weight: 600;
            color: #F79489;
        }

        /* Queue List */
        .queue-section {
            animation: slideInUp 0.8s ease 0.6s both;
        }

        .queue-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 300px;
        }

        .search-box input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #F79489;
            box-shadow: 0 0 0 3px rgba(247, 148, 137, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        .queue-actions {
            display: flex;
            gap: 1rem;
        }

        .action-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .action-btn.primary {
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            color: white;
        }

        .action-btn.secondary {
            background: rgba(255, 255, 255, 0.3);
            color: #666;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        /* Queue List */
        .queue-list {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            overflow: hidden;
        }

        .queue-item {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            display: grid;
            grid-template-columns: auto 1fr auto auto auto;
            gap: 1.5rem;
            align-items: center;
        }

        .queue-item:last-child {
            border-bottom: none;
        }

        .queue-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .ticket-number {
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            color: white;
            padding: 0.75rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            text-align: center;
            min-width: 80px;
        }

        .patient-details {
            flex: 1;
        }

        .patient-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
        }

        .patient-info {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.25rem;
        }

        .service-type {
            font-size: 0.85rem;
            color: #F79489;
            font-weight: 500;
        }

        .priority-badge {
            padding: 0.5rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-badge {
            padding: 0.5rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .wait-time {
            text-align: center;
            color: #666;
            font-weight: 500;
        }

        .item-actions {
            display: flex;
            gap: 0.5rem;
        }

        .item-actions button {
            padding: 0.5rem;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.3);
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .item-actions button:hover {
            background: #F79489;
            color: white;
        }

        /* Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        /* Priority Colors */
        .priority-emergency { background: #fee2e2; color: #dc2626; }
        .priority-faculty { background: #f3e8ff; color: #7c2d12; }
        .priority-personnel { background: #dbeafe; color: #1d4ed8; }
        .priority-student { background: #dcfce7; color: #166534; }
        .priority-regular { background: #f3f4f6; color: #374151; }

        /* Status Colors */
        .status-waiting { background: #fef3c7; color: #92400e; }
        .status-called { background: #dbeafe; color: #1d4ed8; }
        .status-being-served { background: #dcfce7; color: #166534; }
        .status-completed { background: #f3f4f6; color: #374151; }
        .status-no-show { background: #fee2e2; color: #dc2626; }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }

            .queue-item {
                grid-template-columns: 1fr;
                gap: 1rem;
                text-align: center;
            }

            .nav-menu {
                display: none;
            }

            .queue-controls {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Background Orbs -->
    <div class="floating-orb"></div>
    <div class="floating-orb"></div>
    <div class="floating-orb"></div>

    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="dashboard.php" class="logo">
                <i class="fas fa-heartbeat"></i>
                <span>University Clinic</span>
            </a>
            
            <nav>
                <ul class="nav-menu">
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="patients.php">Patients</a></li>
                    <li><a href="appointments.php">Appointments</a></li>
                    <li><a href="queue.php" class="active">Queue</a></li>
                    <li><a href="medical-records.php">Records</a></li>
                </ul>
            </nav>

            <div class="user-menu">
                <div class="user-avatar">AD</div>
                <a href="login.php" style="color: #666; text-decoration: none;">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Queue Management</h1>
                <p class="page-subtitle">Monitor and manage patient queue in real-time</p>
            </div>

            <!-- Queue Overview -->
            <div class="queue-overview">
                <div class="overview-card">
                    <div class="overview-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="overview-number"><?php echo count($queue_tickets); ?></div>
                    <div class="overview-label">Total in Queue</div>
                </div>
                <div class="overview-card">
                    <div class="overview-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="overview-number"><?php echo count(array_filter($queue_tickets, fn($t) => $t['status'] === 'Waiting')); ?></div>
                    <div class="overview-label">Waiting</div>
                </div>
                <div class="overview-card">
                    <div class="overview-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="overview-number"><?php echo count(array_filter($queue_tickets, fn($t) => $t['status'] === 'Being Served')); ?></div>
                    <div class="overview-label">Being Served</div>
                </div>
                <div class="overview-card">
                    <div class="overview-icon">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <div class="overview-number"><?php echo count(array_filter($windows, fn($w) => $w['status'] === 'Available')); ?></div>
                    <div class="overview-label">Available Windows</div>
                </div>
            </div>

            <!-- Windows Status -->
            <div class="windows-section">
                <h2 class="section-title">Service Windows</h2>
                <div class="windows-grid">
                    <?php foreach ($windows as $window): ?>
                    <div class="window-card <?php echo $window['status'] === 'Busy' ? 'busy' : ''; ?>">
                        <div class="window-header">
                            <div class="window-name"><?php echo $window['name']; ?></div>
                            <div class="window-status status-<?php echo strtolower($window['status']); ?>">
                                <?php echo $window['status']; ?>
                            </div>
                        </div>
                        <?php if ($window['current_ticket']): ?>
                        <div class="current-ticket">
                            Now Serving: <?php echo $window['current_ticket']; ?>
                        </div>
                        <?php else: ?>
                        <div style="color: #666; font-style: italic;">Ready for next patient</div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Queue List -->
            <div class="queue-section">
                <div class="queue-controls">
                    <h2 class="section-title">Current Queue</h2>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search queue..." id="searchInput">
                    </div>
                    <div class="queue-actions">
                        <button class="action-btn secondary" onclick="refreshQueue()">
                            <i class="fas fa-sync-alt"></i>
                            Refresh
                        </button>
                        <button class="action-btn primary" onclick="callNext()">
                            <i class="fas fa-volume-up"></i>
                            Call Next
                        </button>
                    </div>
                </div>

                <div class="queue-list">
                    <?php foreach ($queue_tickets as $index => $ticket): ?>
                    <div class="queue-item" style="animation-delay: <?php echo $index * 0.1; ?>s">
                        <div class="ticket-number <?php echo $ticket['status'] === 'Being Served' ? 'pulse' : ''; ?>">
                            <?php echo $ticket['ticket_number']; ?>
                        </div>

                        <div class="patient-details">
                            <div class="patient-name"><?php echo htmlspecialchars($ticket['patient_name']); ?></div>
                            <div class="patient-info"><?php echo htmlspecialchars($ticket['patient_id']); ?> • <?php echo htmlspecialchars($ticket['phone']); ?></div>
                            <div class="service-type"><?php echo htmlspecialchars($ticket['service_type']); ?></div>
                        </div>

                        <div class="priority-badge priority-<?php echo strtolower($ticket['priority_label']); ?>">
                            <i class="fas <?php echo $priority_colors[$ticket['priority']]['icon']; ?>"></i>
                            <?php echo $ticket['priority_label']; ?>
                        </div>

                        <div class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $ticket['status'])); ?>">
                            <?php echo $ticket['status']; ?>
                        </div>

                        <div class="wait-time">
                            <?php echo $ticket['estimated_wait']; ?>
                        </div>

                        <div class="item-actions">
                            <button title="Call Patient" onclick="callPatient('<?php echo $ticket['ticket_number']; ?>')">
                                <i class="fas fa-volume-up"></i>
                            </button>
                            <button title="Mark as Served" onclick="markServed('<?php echo $ticket['ticket_number']; ?>')">
                                <i class="fas fa-check"></i>
                            </button>
                            <button title="Mark as No Show" onclick="markNoShow('<?php echo $ticket['ticket_number']; ?>')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const queueItems = document.querySelectorAll('.queue-item');

            queueItems.forEach(item => {
                const patientName = item.querySelector('.patient-name').textContent.toLowerCase();
                const patientId = item.querySelector('.patient-info').textContent.toLowerCase();
                const ticketNumber = item.querySelector('.ticket-number').textContent.toLowerCase();

                if (patientName.includes(searchTerm) || patientId.includes(searchTerm) || ticketNumber.includes(searchTerm)) {
                    item.style.display = 'grid';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Queue management functions
        function refreshQueue() {
            location.reload();
        }

        function callNext() {
            alert('Calling next patient in queue');
        }

        function callPatient(ticketNumber) {
            alert(`Calling patient ${ticketNumber}`);
        }

        function markServed(ticketNumber) {
            alert(`Marking ${ticketNumber} as served`);
        }

        function markNoShow(ticketNumber) {
            if (confirm(`Mark ${ticketNumber} as no show?`)) {
                alert(`${ticketNumber} marked as no show`);
            }
        }

        // Add staggered animation to queue items
        document.addEventListener('DOMContentLoaded', function() {
            const queueItems = document.querySelectorAll('.queue-item');
            queueItems.forEach((item, index) => {
                item.style.animationDelay = `${0.6 + (index * 0.1)}s`;
                item.style.animation = 'slideInUp 0.8s ease both';
            });

            // Auto-refresh every 30 seconds
            setInterval(refreshQueue, 30000);
        });
    </script>
</body>
</html>
