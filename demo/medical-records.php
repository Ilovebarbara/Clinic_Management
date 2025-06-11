<?php
session_start();

// Simulate authentication check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Sample medical records data
$medical_records = [
    [
        'id' => 1,
        'patient_name' => 'John Doe',
        'patient_id' => 'STU-2023-001',
        'date' => '2024-06-10',
        'chief_complaint' => 'Headache and fever',
        'diagnosis' => 'Viral infection',
        'treatment' => 'Paracetamol 500mg, Rest',
        'doctor' => 'Dr. Smith',
        'follow_up' => '2024-06-17',
        'vitals' => [
            'temperature' => '38.5°C',
            'blood_pressure' => '120/80',
            'heart_rate' => '85 bpm',
            'weight' => '65 kg',
            'height' => '170 cm'
        ],
        'notes' => 'Patient reports significant improvement after treatment'
    ],
    [
        'id' => 2,
        'patient_name' => 'Jane Smith',
        'patient_id' => 'FAC-2023-002',
        'date' => '2024-06-09',
        'chief_complaint' => 'Stomach pain',
        'diagnosis' => 'Gastritis',
        'treatment' => 'Omeprazole 20mg, Dietary advice',
        'doctor' => 'Dr. Johnson',
        'follow_up' => null,
        'vitals' => [
            'temperature' => '36.8°C',
            'blood_pressure' => '115/75',
            'heart_rate' => '78 bpm',
            'weight' => '58 kg',
            'height' => '165 cm'
        ],
        'notes' => 'Advised to avoid spicy foods and stress'
    ],
    [
        'id' => 3,
        'patient_name' => 'Mike Wilson',
        'patient_id' => 'STF-2023-003',
        'date' => '2024-06-08',
        'chief_complaint' => 'Hypertension check-up',
        'diagnosis' => 'Controlled hypertension',
        'treatment' => 'Continue current medication',
        'doctor' => 'Dr. Brown',
        'follow_up' => '2024-07-08',
        'vitals' => [
            'temperature' => '36.5°C',
            'blood_pressure' => '135/85',
            'heart_rate' => '72 bpm',
            'weight' => '78 kg',
            'height' => '175 cm'
        ],
        'notes' => 'Regular monitoring required'
    ],
    [
        'id' => 4,
        'patient_name' => 'Sarah Davis',
        'patient_id' => 'STU-2023-004',
        'date' => '2024-06-07',
        'chief_complaint' => 'Annual physical examination',
        'diagnosis' => 'Healthy',
        'treatment' => 'No treatment required',
        'doctor' => 'Dr. Lee',
        'follow_up' => '2025-06-07',
        'vitals' => [
            'temperature' => '36.7°C',
            'blood_pressure' => '110/70',
            'heart_rate' => '68 bpm',
            'weight' => '55 kg',
            'height' => '162 cm'
        ],
        'notes' => 'All parameters within normal limits'
    ],
    [
        'id' => 5,
        'patient_name' => 'Tom Anderson',
        'patient_id' => 'FAC-2023-005',
        'date' => '2024-06-06',
        'chief_complaint' => 'Sports injury - knee pain',
        'diagnosis' => 'Knee strain',
        'treatment' => 'Ice therapy, Anti-inflammatory medication',
        'doctor' => 'Dr. Garcia',
        'follow_up' => '2024-06-20',
        'vitals' => [
            'temperature' => '36.6°C',
            'blood_pressure' => '125/82',
            'heart_rate' => '75 bpm',
            'weight' => '82 kg',
            'height' => '180 cm'
        ],
        'notes' => 'Avoid strenuous activities for 2 weeks'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records - University Clinic</title>
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

        /* Controls */
        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            gap: 1rem;
            flex-wrap: wrap;
            animation: slideInUp 0.8s ease 0.2s both;
        }

        .search-filters {
            display: flex;
            gap: 1rem;
            flex: 1;
            min-width: 300px;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 400px;
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
            background: rgba(255, 255, 255, 0.3);
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        .filter-select {
            padding: 0.75rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            border-radius: 12px;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            outline: none;
            border-color: #F79489;
            box-shadow: 0 0 0 3px rgba(247, 148, 137, 0.1);
        }

        .add-btn {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(247, 148, 137, 0.3);
        }

        /* Records Grid */
        .records-grid {
            display: grid;
            gap: 1.5rem;
            animation: slideInUp 0.8s ease 0.4s both;
        }

        .record-card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .record-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
        }

        .record-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.3);
        }

        .record-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .patient-info h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
        }

        .patient-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.9rem;
            color: #666;
        }

        .record-date {
            background: rgba(247, 148, 137, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            color: #F79489;
            font-size: 0.9rem;
        }

        .record-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 1.5rem;
        }

        .medical-details {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .detail-section {
            background: rgba(255, 255, 255, 0.3);
            padding: 1rem;
            border-radius: 12px;
        }

        .detail-label {
            font-weight: 600;
            color: #333;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .detail-value {
            color: #666;
            line-height: 1.5;
        }

        .vitals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
        }

        .vital-item {
            background: rgba(247, 148, 137, 0.1);
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
        }

        .vital-label {
            font-size: 0.75rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .vital-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #F79489;
        }

        .follow-up-section {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .follow-up-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #F79489;
            font-weight: 500;
        }

        .record-actions {
            display: flex;
            gap: 0.75rem;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
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
            background: rgba(255, 255, 255, 0.5);
            color: #666;
        }

        .action-btn:hover {
            transform: translateY(-1px);
        }

        .doctor-tag {
            background: rgba(255, 255, 255, 0.5);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            color: #666;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
            .main-content {
                padding: 1rem;
            }

            .record-content {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .controls {
                flex-direction: column;
                align-items: stretch;
            }

            .search-filters {
                flex-direction: column;
            }

            .nav-menu {
                display: none;
            }

            .patient-meta {
                flex-direction: column;
                gap: 0.5rem;
            }

            .follow-up-section {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
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
                    <li><a href="queue.php">Queue</a></li>
                    <li><a href="medical-records.php" class="active">Records</a></li>
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
                <h1 class="page-title">Medical Records</h1>
                <p class="page-subtitle">Access and manage patient medical history</p>
            </div>

            <!-- Controls -->
            <div class="controls">
                <div class="search-filters">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search records..." id="searchInput">
                    </div>
                    <select class="filter-select" id="doctorFilter">
                        <option value="">All Doctors</option>
                        <option value="Dr. Smith">Dr. Smith</option>
                        <option value="Dr. Johnson">Dr. Johnson</option>
                        <option value="Dr. Brown">Dr. Brown</option>
                        <option value="Dr. Lee">Dr. Lee</option>
                        <option value="Dr. Garcia">Dr. Garcia</option>
                    </select>
                    <input type="date" class="filter-select" id="dateFilter">
                </div>
                <button class="add-btn" onclick="addNewRecord()">
                    <i class="fas fa-plus"></i>
                    New Record
                </button>
            </div>

            <!-- Records Grid -->
            <div class="records-grid" id="recordsGrid">
                <?php foreach ($medical_records as $index => $record): ?>
                <div class="record-card" style="animation-delay: <?php echo $index * 0.1; ?>s">
                    <div class="record-header">
                        <div class="patient-info">
                            <h3><?php echo htmlspecialchars($record['patient_name']); ?></h3>
                            <div class="patient-meta">
                                <span><?php echo htmlspecialchars($record['patient_id']); ?></span>
                                <div class="doctor-tag">
                                    <i class="fas fa-user-md"></i>
                                    <?php echo htmlspecialchars($record['doctor']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="record-date">
                            <?php echo date('M j, Y', strtotime($record['date'])); ?>
                        </div>
                    </div>

                    <div class="record-content">
                        <div class="medical-details">
                            <div class="detail-section">
                                <div class="detail-label">Chief Complaint</div>
                                <div class="detail-value"><?php echo htmlspecialchars($record['chief_complaint']); ?></div>
                            </div>

                            <div class="detail-section">
                                <div class="detail-label">Diagnosis</div>
                                <div class="detail-value"><?php echo htmlspecialchars($record['diagnosis']); ?></div>
                            </div>

                            <div class="detail-section">
                                <div class="detail-label">Treatment</div>
                                <div class="detail-value"><?php echo htmlspecialchars($record['treatment']); ?></div>
                            </div>

                            <?php if (isset($record['notes'])): ?>
                            <div class="detail-section">
                                <div class="detail-label">Notes</div>
                                <div class="detail-value"><?php echo htmlspecialchars($record['notes']); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="vitals-section">
                            <div class="detail-label" style="margin-bottom: 1rem;">Vital Signs</div>
                            <div class="vitals-grid">
                                <div class="vital-item">
                                    <div class="vital-label">Temperature</div>
                                    <div class="vital-value"><?php echo $record['vitals']['temperature']; ?></div>
                                </div>
                                <div class="vital-item">
                                    <div class="vital-label">Blood Pressure</div>
                                    <div class="vital-value"><?php echo $record['vitals']['blood_pressure']; ?></div>
                                </div>
                                <div class="vital-item">
                                    <div class="vital-label">Heart Rate</div>
                                    <div class="vital-value"><?php echo $record['vitals']['heart_rate']; ?></div>
                                </div>
                                <div class="vital-item">
                                    <div class="vital-label">Weight</div>
                                    <div class="vital-value"><?php echo $record['vitals']['weight']; ?></div>
                                </div>
                                <div class="vital-item">
                                    <div class="vital-label">Height</div>
                                    <div class="vital-value"><?php echo $record['vitals']['height']; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="follow-up-section">
                        <div class="follow-up-date">
                            <?php if ($record['follow_up']): ?>
                                <i class="fas fa-calendar-check"></i>
                                Follow-up: <?php echo date('M j, Y', strtotime($record['follow_up'])); ?>
                            <?php else: ?>
                                <i class="fas fa-check-circle"></i>
                                No follow-up required
                            <?php endif; ?>
                        </div>

                        <div class="record-actions">
                            <button class="action-btn secondary" onclick="editRecord(<?php echo $record['id']; ?>)">
                                <i class="fas fa-edit"></i>
                                Edit
                            </button>
                            <button class="action-btn primary" onclick="viewRecord(<?php echo $record['id']; ?>)">
                                <i class="fas fa-eye"></i>
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <script>
        // Search and Filter functionality
        const searchInput = document.getElementById('searchInput');
        const doctorFilter = document.getElementById('doctorFilter');
        const dateFilter = document.getElementById('dateFilter');
        const recordsGrid = document.getElementById('recordsGrid');

        function filterRecords() {
            const searchTerm = searchInput.value.toLowerCase();
            const doctorValue = doctorFilter.value;
            const dateValue = dateFilter.value;
            const cards = recordsGrid.querySelectorAll('.record-card');

            cards.forEach(card => {
                const patientName = card.querySelector('.patient-info h3').textContent.toLowerCase();
                const patientId = card.querySelector('.patient-meta span').textContent.toLowerCase();
                const doctor = card.querySelector('.doctor-tag').textContent.trim();
                const recordDate = card.querySelector('.record-date').textContent;

                const matchesSearch = patientName.includes(searchTerm) || patientId.includes(searchTerm);
                const matchesDoctor = !doctorValue || doctor.includes(doctorValue);
                const matchesDate = !dateValue || recordDate.includes(new Date(dateValue).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }));

                if (matchesSearch && matchesDoctor && matchesDate) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', filterRecords);
        doctorFilter.addEventListener('change', filterRecords);
        dateFilter.addEventListener('change', filterRecords);

        // Action functions
        function addNewRecord() {
            alert('Add New Medical Record modal would open here');
        }

        function editRecord(id) {
            alert(`Edit medical record ${id}`);
        }

        function viewRecord(id) {
            alert(`View detailed medical record ${id}`);
        }

        // Add staggered animation to cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.record-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${0.4 + (index * 0.1)}s`;
                card.style.animation = 'slideInUp 0.8s ease both';
            });
        });
    </script>
</body>
</html>
