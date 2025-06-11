<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Management | UniClinic</title>
    
    <!-- Font Imports -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #2d3748;
            background: linear-gradient(135deg, #F9F1F0 0%, #FADCD9 100%);
            background-attachment: fixed;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Animated Background */
        .bg-animated {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            pointer-events: none;
        }
        
        .floating-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            animation: floatOrb 25s ease-in-out infinite;
        }
        
        .orb-1 {
            top: 10%;
            left: 5%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(247,148,137,0.1) 0%, transparent 70%);
            animation-delay: 0s;
        }
        
        .orb-2 {
            top: 50%;
            right: 10%;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(248,175,166,0.08) 0%, transparent 70%);
            animation-delay: 8s;
        }
        
        .orb-3 {
            bottom: 20%;
            left: 15%;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(250,220,217,0.12) 0%, transparent 70%);
            animation-delay: 16s;
        }
        
        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(247,148,137,0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            height: 80px;
        }
        
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem;
            height: 100%;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #2d3748;
            font-size: 1.5rem;
            font-weight: 800;
        }
        
        .logo-icon {
            font-size: 2rem;
            margin-right: 0.5rem;
            color: #F79489;
            filter: drop-shadow(0 0 10px rgba(247,148,137,0.3));
        }
        
        .logo-text {
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }
        
        .nav-link {
            text-decoration: none;
            color: rgba(45,55,72,0.8);
            font-weight: 600;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
            backdrop-filter: blur(10px);
        }
        
        .nav-link:hover, .nav-link.active {
            color: #2d3748;
            background: rgba(247,148,137,0.1);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(247,148,137,0.2);
            border-color: rgba(247,148,137,0.3);
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
            margin-top: 80px;
            padding: 2rem;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .page-header {
            margin-bottom: 3rem;
            animation: slideInUp 1s ease-out;
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            font-size: 1.1rem;
            color: rgba(45,55,72,0.7);
            font-weight: 500;
        }
        
        /* Action Bar */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .search-section {
            display: flex;
            gap: 1rem;
            flex: 1;
            min-width: 300px;
        }
        
        .search-input {
            flex: 1;
            padding: 1rem 1.2rem;
            border: 2px solid rgba(247,148,137,0.1);
            border-radius: 16px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #F79489;
            box-shadow: 0 0 0 4px rgba(247,148,137,0.1);
        }
        
        .filter-select {
            padding: 1rem 1.2rem;
            border: 2px solid rgba(247,148,137,0.1);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(247,148,137,0.3);
            color: white;
            text-decoration: none;
        }
        
        /* Patients Grid */
        .patients-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .patient-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(247,148,137,0.1);
            border-radius: 20px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            animation: slideInUp 1s ease-out;
        }
        
        .patient-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(247,148,137,0.15);
        }
        
        .patient-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
        }
        
        .patient-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        
        .patient-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .patient-info {
            flex: 1;
            margin-left: 1rem;
        }
        
        .patient-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }
        
        .patient-id {
            font-size: 0.9rem;
            color: rgba(45,55,72,0.6);
            font-weight: 500;
        }
        
        .patient-type {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .type-student {
            background: rgba(59, 130, 246, 0.1);
            color: #1D4ED8;
        }
        
        .type-faculty {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }
        
        .type-staff {
            background: rgba(245, 158, 11, 0.1);
            color: #D97706;
        }
        
        .patient-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-size: 0.8rem;
            color: rgba(45,55,72,0.6);
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }
        
        .detail-value {
            font-size: 0.9rem;
            color: #2d3748;
            font-weight: 500;
        }
        
        .patient-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }
        
        .btn-action {
            padding: 0.6rem 1rem;
            border: none;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .btn-view {
            background: rgba(59, 130, 246, 0.1);
            color: #1D4ED8;
        }
        
        .btn-edit {
            background: rgba(245, 158, 11, 0.1);
            color: #D97706;
        }
        
        .btn-medical {
            background: rgba(139, 92, 246, 0.1);
            color: #7C3AED;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            text-decoration: none;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(10px);
            z-index: 2000;
            animation: fadeIn 0.3s ease;
        }
        
        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            animation: slideInScale 0.3s ease;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(247,148,137,0.2);
        }
        
        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: rgba(45,55,72,0.6);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .modal-close:hover {
            background: rgba(247,148,137,0.1);
            color: #F79489;
        }
        
        /* Form Styles */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        
        .form-input, .form-select, .form-textarea {
            padding: 1rem;
            border: 2px solid rgba(247,148,137,0.1);
            border-radius: 12px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #F79489;
            box-shadow: 0 0 0 4px rgba(247,148,137,0.1);
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        /* Animations */
        @keyframes floatOrb {
            0%, 100% { transform: translateY(0px) translateX(0px); }
            25% { transform: translateY(-30px) translateX(20px); }
            50% { transform: translateY(-15px) translateX(-25px); }
            75% { transform: translateY(-35px) translateX(10px); }
        }
        
        @keyframes slideInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0px);
            }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideInScale {
            0% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.9);
            }
            100% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                padding: 1rem;
            }
            
            .nav-menu {
                display: none;
            }
            
            .main-content {
                padding: 1rem;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .patients-grid {
                grid-template-columns: 1fr;
            }
            
            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-section {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animated">
        <div class="floating-orb orb-1"></div>
        <div class="floating-orb orb-2"></div>
        <div class="floating-orb orb-3"></div>
    </div>
    
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="dashboard.php" class="logo-container">
                <i class="fas fa-heartbeat logo-icon"></i>
                <span class="logo-text">UniClinic</span>
            </a>
            
            <nav>
                <ul class="nav-menu">
                    <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                    <li><a href="patients.php" class="nav-link active">Patients</a></li>
                    <li><a href="appointments.php" class="nav-link">Appointments</a></li>
                    <li><a href="queue.php" class="nav-link">Queue</a></li>
                    <li><a href="medical-records.php" class="nav-link">Records</a></li>
                </ul>
            </nav>
            
            <div class="user-menu">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <section class="page-header">
            <h1 class="page-title">Patient Management</h1>
            <p class="page-subtitle">Manage patient records and information</p>
        </section>
        
        <!-- Action Bar -->
        <section class="action-bar">
            <div class="search-section">
                <input type="text" class="search-input" placeholder="Search patients by name, ID, or email..." id="searchInput">
                <select class="filter-select" id="typeFilter">
                    <option value="">All Types</option>
                    <option value="student">Students</option>
                    <option value="faculty">Faculty</option>
                    <option value="staff">Staff</option>
                </select>
            </div>
            <button class="btn-primary" onclick="openAddPatientModal()">
                <i class="fas fa-plus"></i>
                Add New Patient
            </button>
        </section>
        
        <!-- Patients Grid -->
        <section class="patients-grid" id="patientsGrid">
            <!-- Patient cards will be populated by JavaScript -->
        </section>
    </main>
    
    <!-- Add Patient Modal -->
    <div class="modal" id="addPatientModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add New Patient</h2>
                <button class="modal-close" onclick="closeModal('addPatientModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form class="form-grid" id="addPatientForm">
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Patient Type</label>
                    <select class="form-select" required>
                        <option value="">Select Type</option>
                        <option value="student">Student</option>
                        <option value="faculty">Faculty</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Blood Type</label>
                    <select class="form-select">
                        <option value="">Select Blood Type</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="form-label">Address</label>
                    <textarea class="form-textarea" required></textarea>
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="form-label">Known Allergies</label>
                    <textarea class="form-textarea" placeholder="List any known allergies..."></textarea>
                </div>
                <div class="form-group" style="grid-column: 1 / -1; margin-top: 1rem;">
                    <button type="submit" class="btn-primary" style="width: 100%;">
                        <i class="fas fa-save"></i>
                        Register Patient
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Sample patients data
        const patients = [
            {
                id: 1,
                patientId: 'STU-2024-001',
                name: 'John Doe',
                type: 'student',
                email: 'john.doe@university.edu',
                phone: '+1234567890',
                campus: 'Main Campus',
                college: 'Engineering',
                bloodType: 'O+',
                lastVisit: '2024-06-10',
                status: 'Active'
            },
            {
                id: 2,
                patientId: 'FAC-2024-002',
                name: 'Dr. Jane Smith',
                type: 'faculty',
                email: 'jane.smith@university.edu',
                phone: '+1234567892',
                campus: 'Main Campus',
                college: 'Liberal Arts',
                bloodType: 'A+',
                lastVisit: '2024-06-09',
                status: 'Active'
            },
            {
                id: 3,
                patientId: 'STF-2024-003',
                name: 'Mike Wilson',
                type: 'staff',
                email: 'mike.wilson@university.edu',
                phone: '+1234567893',
                campus: 'Main Campus',
                college: 'Administration',
                bloodType: 'B+',
                lastVisit: '2024-06-08',
                status: 'Active'
            },
            {
                id: 4,
                patientId: 'STU-2024-004',
                name: 'Sarah Johnson',
                type: 'student',
                email: 'sarah.johnson@university.edu',
                phone: '+1234567894',
                campus: 'Main Campus',
                college: 'Medicine',
                bloodType: 'AB+',
                lastVisit: '2024-06-07',
                status: 'Active'
            },
            {
                id: 5,
                patientId: 'FAC-2024-005',
                name: 'Prof. Robert Brown',
                type: 'faculty',
                email: 'robert.brown@university.edu',
                phone: '+1234567895',
                campus: 'Main Campus',
                college: 'Science',
                bloodType: 'O-',
                lastVisit: '2024-06-06',
                status: 'Active'
            },
            {
                id: 6,
                patientId: 'STU-2024-006',
                name: 'Emily Davis',
                type: 'student',
                email: 'emily.davis@university.edu',
                phone: '+1234567896',
                campus: 'Main Campus',
                college: 'Business',
                bloodType: 'A-',
                lastVisit: '2024-06-05',
                status: 'Active'
            }
        ];
        
        // Generate patient card HTML
        function createPatientCard(patient) {
            const typeClass = `type-${patient.type}`;
            const initials = patient.name.split(' ').map(n => n[0]).join('');
            
            return `
                <div class="patient-card">
                    <div class="patient-header">
                        <div style="display: flex; align-items: center; flex: 1;">
                            <div class="patient-avatar">${initials}</div>
                            <div class="patient-info">
                                <div class="patient-name">${patient.name}</div>
                                <div class="patient-id">${patient.patientId}</div>
                            </div>
                        </div>
                        <div class="patient-type ${typeClass}">
                            ${patient.type}
                        </div>
                    </div>
                    
                    <div class="patient-details">
                        <div class="detail-item">
                            <div class="detail-label">Email</div>
                            <div class="detail-value">${patient.email}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Phone</div>
                            <div class="detail-value">${patient.phone}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">College</div>
                            <div class="detail-value">${patient.college}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Blood Type</div>
                            <div class="detail-value">${patient.bloodType}</div>
                        </div>
                    </div>
                    
                    <div class="patient-actions">
                        <button class="btn-action btn-view" onclick="viewPatient(${patient.id})">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn-action btn-edit" onclick="editPatient(${patient.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn-action btn-medical" onclick="viewMedicalHistory(${patient.id})">
                            <i class="fas fa-file-medical"></i> History
                        </button>
                    </div>
                </div>
            `;
        }
        
        // Render patients
        function renderPatients(filteredPatients = patients) {
            const grid = document.getElementById('patientsGrid');
            grid.innerHTML = filteredPatients.map(createPatientCard).join('');
            
            // Add staggered animation
            const cards = grid.querySelectorAll('.patient-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        }
        
        // Search and filter functionality
        function filterPatients() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const typeFilter = document.getElementById('typeFilter').value;
            
            const filtered = patients.filter(patient => {
                const matchesSearch = patient.name.toLowerCase().includes(searchTerm) ||
                                    patient.patientId.toLowerCase().includes(searchTerm) ||
                                    patient.email.toLowerCase().includes(searchTerm);
                
                const matchesType = !typeFilter || patient.type === typeFilter;
                
                return matchesSearch && matchesType;
            });
            
            renderPatients(filtered);
        }
        
        // Modal functions
        function openAddPatientModal() {
            document.getElementById('addPatientModal').style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Patient actions
        function viewPatient(id) {
            const patient = patients.find(p => p.id === id);
            alert(`Viewing patient: ${patient.name}\nID: ${patient.patientId}`);
        }
        
        function editPatient(id) {
            const patient = patients.find(p => p.id === id);
            alert(`Editing patient: ${patient.name}`);
        }
        
        function viewMedicalHistory(id) {
            const patient = patients.find(p => p.id === id);
            window.location.href = `medical-records.php?patient=${id}`;
        }
        
        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            renderPatients();
            
            document.getElementById('searchInput').addEventListener('input', filterPatients);
            document.getElementById('typeFilter').addEventListener('change', filterPatients);
            
            // Form submission
            document.getElementById('addPatientForm').addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Patient registration functionality would be implemented here');
                closeModal('addPatientModal');
            });
            
            // Close modal when clicking outside
            document.getElementById('addPatientModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal('addPatientModal');
                }
            });
        });
    </script>
</body>
</html>
