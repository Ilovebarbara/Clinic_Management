<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sample patient data
$patient_data = [
    'name' => 'John Doe',
    'id' => 'STU-2023-001',
    'email' => 'john.doe@university.edu',
    'phone' => '+1 (555) 123-4567',
    'appointments' => [
        [
            'date' => '2024-06-17',
            'time' => '2:00 PM',
            'doctor' => 'Dr. Smith',
            'purpose' => 'Follow-up consultation',
            'status' => 'Confirmed'
        ],
        [
            'date' => '2024-06-24',
            'time' => '10:00 AM',
            'doctor' => 'Dr. Johnson',
            'purpose' => 'Annual check-up',
            'status' => 'Scheduled'
        ]
    ],
    'medical_history' => [
        [
            'date' => '2024-06-10',
            'diagnosis' => 'Viral infection',
            'doctor' => 'Dr. Smith',
            'treatment' => 'Paracetamol 500mg, Rest'
        ],
        [
            'date' => '2024-05-15',
            'diagnosis' => 'Health screening',
            'doctor' => 'Dr. Lee',
            'treatment' => 'All parameters normal'
        ]
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Portal - University Clinic</title>
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
            background: linear-gradient(135deg, #F9F1F0 0%, #FADCD9 50%, #F8AFA6 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Floating Background Orbs */
        .floating-orb {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
            animation: floatOrb 20s infinite ease-in-out;
        }

        .floating-orb:nth-child(1) {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(247,148,137,0.15), transparent);
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-orb:nth-child(2) {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(248,175,166,0.12), transparent);
            top: 60%;
            right: 15%;
            animation-delay: -7s;
        }

        .floating-orb:nth-child(3) {
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(250,220,217,0.18), transparent);
            bottom: 20%;
            left: 20%;
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
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(247,148,137,0.2);
            position: sticky;
            top: 0;
            z-index: 100;
            padding: 1rem 0;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
        }

        .logo i {
            font-size: 2rem;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
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
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-menu a:hover,
        .nav-menu a.active {
            color: #F79489;
            background: rgba(247,148,137,0.1);
        }

        .user-info {
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        /* Welcome Section */
        .welcome-section {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
            animation: slideInUp 0.8s ease;
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #333 0%, #666 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .welcome-subtitle {
            color: #666;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .patient-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .detail-item {
            background: rgba(255, 255, 255, 0.5);
            padding: 1rem;
            border-radius: 12px;
            text-align: left;
        }

        .detail-label {
            font-size: 0.85rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .detail-value {
            font-weight: 600;
            color: #333;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .action-card {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            animation: slideInUp 0.8s ease;
        }

        .action-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.4);
        }

        .action-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            box-shadow: 0 10px 30px rgba(247, 148, 137, 0.3);
        }

        .action-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .action-description {
            color: #666;
            line-height: 1.6;
        }

        /* Content Sections */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .section {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 2rem;
            animation: slideInUp 0.8s ease;
        }

        .section-title {
            font-size: 1.5rem;
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
        .appointment-item {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            border-left: 4px solid #F79489;
        }

        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }

        .appointment-date {
            font-weight: 600;
            color: #333;
        }

        .appointment-time {
            color: #F79489;
            font-weight: 500;
        }

        .appointment-details {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-confirmed {
            background: #dcfce7;
            color: #166534;
        }

        .status-scheduled {
            background: #dbeafe;
            color: #1d4ed8;
        }

        /* Medical History */
        .history-item {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            border-left: 4px solid #F8AFA6;
        }

        .history-date {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .history-diagnosis {
            color: #F79489;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .history-doctor {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .history-treatment {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.4;
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

        /* Responsive */
        @media (max-width: 768px) {
            .header-container {
                padding: 0 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .nav-menu {
                gap: 1rem;
            }

            .main-content {
                padding: 1rem;
            }

            .welcome-title {
                font-size: 2rem;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .quick-actions {
                grid-template-columns: 1fr;
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
        <div class="header-container">
            <a href="#" class="logo">
                <i class="fas fa-heartbeat"></i>
                <span class="logo-text">University Clinic</span>
            </a>

            <nav>
                <ul class="nav-menu">
                    <li><a href="#dashboard" class="active">Dashboard</a></li>
                    <li><a href="#appointments">Appointments</a></li>
                    <li><a href="#medical-history">Medical History</a></li>
                    <li><a href="#profile">Profile</a></li>
                    <li><a href="#help">Help</a></li>
                </ul>
            </nav>

            <div class="user-info">
                <div class="user-avatar">JD</div>                <span style="color: #666; font-weight: 500;"><?php echo $patient_data['name']; ?></span>
                <a href="/logout" style="color: #F79489; text-decoration: none; margin-left: 1rem;">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1 class="welcome-title">Welcome back, <?php echo explode(' ', $patient_data['name'])[0]; ?>!</h1>
            <p class="welcome-subtitle">Manage your health appointments and records</p>
            
            <div class="patient-details">
                <div class="detail-item">
                    <div class="detail-label">Patient ID</div>
                    <div class="detail-value"><?php echo $patient_data['id']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div class="detail-value"><?php echo $patient_data['email']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Phone</div>
                    <div class="detail-value"><?php echo $patient_data['phone']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Status</div>
                    <div class="detail-value" style="color: #10b981;">Active Patient</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="action-card" onclick="bookAppointment()">
                <div class="action-icon">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <h3 class="action-title">Book New Appointment</h3>
                <p class="action-description">Schedule a consultation with our medical professionals</p>
            </div>

            <div class="action-card" onclick="joinQueue()">
                <div class="action-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h3 class="action-title">Join Walk-in Queue</h3>
                <p class="action-description">Get a queue number for immediate consultation</p>
            </div>

            <div class="action-card" onclick="requestCertificate()">
                <div class="action-icon">
                    <i class="fas fa-certificate"></i>
                </div>
                <h3 class="action-title">Request Medical Certificate</h3>
                <p class="action-description">Download health certificates for various purposes</p>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Upcoming Appointments -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-calendar-check"></i>
                    Upcoming Appointments
                </h2>

                <?php foreach ($patient_data['appointments'] as $appointment): ?>
                <div class="appointment-item">
                    <div class="appointment-header">
                        <div>
                            <div class="appointment-date"><?php echo date('M j, Y', strtotime($appointment['date'])); ?></div>
                            <div class="appointment-time"><?php echo $appointment['time']; ?></div>
                        </div>
                        <div class="status-badge status-<?php echo strtolower($appointment['status']); ?>">
                            <?php echo $appointment['status']; ?>
                        </div>
                    </div>
                    <div class="appointment-details">
                        <strong><?php echo $appointment['doctor']; ?></strong><br>
                        <?php echo $appointment['purpose']; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <button onclick="viewAllAppointments()" style="width: 100%; padding: 0.75rem; background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; margin-top: 1rem;">
                    View All Appointments
                </button>
            </div>

            <!-- Recent Medical History -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-file-medical"></i>
                    Recent Medical History
                </h2>

                <?php foreach ($patient_data['medical_history'] as $record): ?>
                <div class="history-item">
                    <div class="history-date"><?php echo date('M j, Y', strtotime($record['date'])); ?></div>
                    <div class="history-diagnosis"><?php echo $record['diagnosis']; ?></div>
                    <div class="history-doctor">Treated by: <?php echo $record['doctor']; ?></div>
                    <div class="history-treatment"><?php echo $record['treatment']; ?></div>
                </div>
                <?php endforeach; ?>

                <button onclick="viewMedicalHistory()" style="width: 100%; padding: 0.75rem; background: rgba(247, 148, 137, 0.1); color: #F79489; border: 1px solid #F79489; border-radius: 12px; font-weight: 600; cursor: pointer; margin-top: 1rem;">
                    View Complete History
                </button>
            </div>
        </div>
    </main>

    <?php include 'quick-nav.php'; ?>

    <script>
        // Quick action functions
        function bookAppointment() {
            alert('Book Appointment\n\nRedirecting to appointment booking system...\n\nAvailable slots:\n• Tomorrow 2:00 PM - Dr. Smith\n• June 19, 10:00 AM - Dr. Johnson\n• June 20, 3:00 PM - Dr. Brown');
        }

        function joinQueue() {
            const queueNumber = 'Q' + String(Math.floor(Math.random() * 900) + 100);
            alert(`Queue Number Generated!\n\nYour number: ${queueNumber}\nEstimated wait: 15-20 minutes\n\nPlease visit the clinic and wait for your number to be called.`);
        }

        function requestCertificate() {
            alert('Medical Certificate Request\n\nAvailable certificates:\n• Health Clearance Certificate\n• Fitness for Travel Certificate\n• Medical Leave Certificate\n• Vaccination Certificate\n\nPlease visit the clinic with valid ID to collect your certificate.');
        }

        function viewAllAppointments() {
            alert('All Appointments\n\nRedirecting to appointments page...');
        }

        function viewMedicalHistory() {
            alert('Complete Medical History\n\nRedirecting to medical records page...');
        }

        // Add smooth scrolling for navigation
        document.querySelectorAll('.nav-menu a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = this.getAttribute('href');
                if (target.startsWith('#')) {
                    const element = document.querySelector(target);
                    if (element) {
                        element.scrollIntoView({ behavior: 'smooth' });
                    }
                }
            });
        });

        // Add staggered animations
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.action-card, .section');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${0.2 + (index * 0.15)}s`;
            });
        });
    </script>
</body>
</html>
