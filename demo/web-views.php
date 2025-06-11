<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Views - University Clinic Management System</title>
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
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 50%, #FADCD9 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Floating orbs background */
        .orb {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            animation: floatOrb 6s ease-in-out infinite;
            z-index: 1;
        }

        .orb:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .orb:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .orb:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        .orb:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 30%;
            right: 30%;
            animation-delay: 1s;
        }

        @keyframes floatOrb {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .container {
            position: relative;
            z-index: 10;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            min-height: 100vh;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .logo {
            background: linear-gradient(135deg, #F79489, #F8AFA6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .logo i {
            color: #F79489;
            animation: heartbeat 2s ease-in-out infinite;
        }

        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.2rem;
            font-weight: 300;
        }

        .views-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .view-category {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .view-category:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .category-title {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .category-title i {
            color: #F79489;
            font-size: 1.2rem;
        }

        .view-links {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .view-link {
            display: block;
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .view-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .view-link:hover::before {
            left: 100%;
        }

        .view-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }

        .view-link i {
            margin-right: 0.5rem;
            color: #F79489;
        }

        .description {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin-top: 0.5rem;
            line-height: 1.4;
        }

        .features-section {
            margin-top: 3rem;
            text-align: center;
        }

        .features-title {
            color: white;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            color: #F79489;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .feature-title {
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .feature-desc {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            line-height: 1.4;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .views-grid {
                grid-template-columns: 1fr;
            }
            
            .logo {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Floating orbs background -->
    <div class="orb"></div>
    <div class="orb"></div>
    <div class="orb"></div>
    <div class="orb"></div>

    <div class="container">
        <div class="header">
            <h1 class="logo">
                <i class="fas fa-heartbeat"></i>
                University Clinic Management System
            </h1>
            <p class="subtitle">Web Views & Interface Showcase</p>
        </div>

        <div class="views-grid">
            <!-- Web Interfaces -->
            <div class="view-category">
                <h2 class="category-title">
                    <i class="fas fa-globe"></i>
                    Web Interfaces
                </h2>
                <div class="view-links">
                    <a href="patient-portal.php" class="view-link">
                        <i class="fas fa-user-injured"></i>
                        Patient Web Portal
                        <div class="description">Comprehensive web interface for patients to manage appointments, view medical history, and access clinic services.</div>
                    </a>
                    <a href="staff-dashboard.php" class="view-link">
                        <i class="fas fa-user-md"></i>
                        Staff Dashboard
                        <div class="description">Professional web interface for doctors and staff with patient management, scheduling, and real-time queue monitoring.</div>
                    </a>
                    <a href="admin-panel.php" class="view-link">
                        <i class="fas fa-cog"></i>
                        Admin Panel
                        <div class="description">Administrative interface for system management, user administration, analytics, and system maintenance.</div>
                    </a>
                </div>
            </div>

            <!-- Core System -->
            <div class="view-category">
                <h2 class="category-title">
                    <i class="fas fa-desktop"></i>
                    Core System
                </h2>                <div class="view-links">
                    <a href="/login" class="view-link">
                        <i class="fas fa-sign-in-alt"></i>
                        Login System
                        <div class="description">Secure authentication gateway with role-based access control and beautiful glass morphism design.</div>
                    </a>
                    <a href="/dashboard" class="view-link">
                        <i class="fas fa-tachometer-alt"></i>
                        Main Dashboard
                        <div class="description">Central command center with statistics overview, quick actions, and system status monitoring.</div>
                    </a>
                    <a href="patients.php" class="view-link">
                        <i class="fas fa-users"></i>
                        Patient Management
                        <div class="description">Comprehensive patient database with search, filtering, and detailed patient information cards.</div>
                    </a>
                    <a href="appointments.php" class="view-link">
                        <i class="fas fa-calendar-check"></i>
                        Appointment System
                        <div class="description">Advanced appointment scheduling with calendar views, status tracking, and automated notifications.</div>
                    </a>
                </div>
            </div>

            <!-- Specialized Interfaces -->
            <div class="view-category">
                <h2 class="category-title">
                    <i class="fas fa-tablet-alt"></i>
                    Specialized Interfaces
                </h2>
                <div class="view-links">
                    <a href="kiosk.php" class="view-link">
                        <i class="fas fa-desktop"></i>
                        Kiosk Interface
                        <div class="description">Touch-friendly kiosk interface for self-service check-ins, queue management, and patient information updates.</div>
                    </a>
                    <a href="mobile.php" class="view-link">
                        <i class="fas fa-mobile-alt"></i>
                        Mobile Portal
                        <div class="description">Native mobile app experience with bottom navigation, optimized for smartphones and tablets.</div>
                    </a>
                    <a href="queue.php" class="view-link">
                        <i class="fas fa-list-ol"></i>
                        Queue Management
                        <div class="description">Real-time queue monitoring with priority indicators, estimated wait times, and digital signage display.</div>
                    </a>
                    <a href="medical-records.php" class="view-link">
                        <i class="fas fa-file-medical"></i>
                        Medical Records
                        <div class="description">Comprehensive medical record system with vitals tracking, diagnosis history, and treatment documentation.</div>
                    </a>
                </div>
            </div>
        </div>

        <div class="features-section">
            <h2 class="features-title">Key Features</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-paint-brush"></i>
                    </div>
                    <div class="feature-title">Beautiful Design</div>
                    <div class="feature-desc">Glass morphism effects, gradient backgrounds, and smooth animations throughout all interfaces</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="feature-title">Responsive</div>
                    <div class="feature-desc">Fully responsive design that works perfectly on desktop, tablet, and mobile devices</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div class="feature-title">Multi-Role Support</div>
                    <div class="feature-desc">Specialized interfaces for patients, staff, administrators, and self-service kiosks</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="feature-title">Real-time Updates</div>
                    <div class="feature-desc">Live queue management, appointment status updates, and real-time system monitoring</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
