<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniClinic Dashboard | Advanced Healthcare Management</title>
    
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
        
        .welcome-section {
            margin-bottom: 3rem;
            text-align: center;
            animation: slideInUp 1s ease-out;
        }
        
        .welcome-title {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        
        .welcome-subtitle {
            font-size: 1.2rem;
            color: rgba(45,55,72,0.7);
            font-weight: 500;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
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
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(247,148,137,0.15);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
        }
        
        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .stat-icon.patients {
            background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
        }
        
        .stat-icon.appointments {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        }
        
        .stat-icon.queue {
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        }
        
        .stat-icon.records {
            background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: rgba(45,55,72,0.7);
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .stat-change {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .stat-change.positive {
            color: #10B981;
        }
        
        .stat-change.negative {
            color: #EF4444;
        }
        
        /* Quick Actions */
        .quick-actions {
            margin-bottom: 3rem;
        }
        
        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        
        .action-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(247,148,137,0.1);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            text-decoration: none;
            color: #2d3748;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(247,148,137,0.2);
            color: #2d3748;
            text-decoration: none;
        }
        
        .action-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #F79489;
        }
        
        .action-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .action-desc {
            font-size: 0.9rem;
            color: rgba(45,55,72,0.7);
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
            
            .welcome-title {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .actions-grid {
                grid-template-columns: repeat(2, 1fr);
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
            <a href="#" class="logo-container">
                <i class="fas fa-heartbeat logo-icon"></i>
                <span class="logo-text">UniClinic</span>
            </a>
            
            <nav>
                <ul class="nav-menu">                    <li><a href="/dashboard" class="nav-link active">Dashboard</a></li>
                    <li><a href="/patients" class="nav-link">Patients</a></li>
                    <li><a href="/appointments" class="nav-link">Appointments</a></li>
                    <li><a href="/queue" class="nav-link">Queue</a></li>
                    <li><a href="/medical-records" class="nav-link">Records</a></li>
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
        <!-- Welcome Section -->
        <section class="welcome-section">
            <h1 class="welcome-title">Welcome to UniClinic</h1>
            <p class="welcome-subtitle">Advanced Healthcare Management Dashboard</p>
        </section>
        
        <!-- Statistics Grid -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-value">1,247</div>
                        <div class="stat-label">Total Patients</div>
                    </div>
                    <div class="stat-icon patients">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>+12% from last month</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-value">156</div>
                        <div class="stat-label">Today's Appointments</div>
                    </div>
                    <div class="stat-icon appointments">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>+8% from yesterday</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-value">23</div>
                        <div class="stat-label">Queue Waiting</div>
                    </div>
                    <div class="stat-icon queue">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-change negative">
                    <i class="fas fa-arrow-down"></i>
                    <span>-5% from peak hour</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-value">89</div>
                        <div class="stat-label">Records Today</div>
                    </div>
                    <div class="stat-icon records">
                        <i class="fas fa-file-medical"></i>
                    </div>
                </div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>+15% efficiency</span>
                </div>
            </div>
        </section>
        
        <!-- Quick Actions -->
        <section class="quick-actions">
            <h2 class="section-title">Quick Actions</h2>
            <div class="actions-grid">
                <a href="/patients" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="action-title">Add Patient</div>
                    <div class="action-desc">Register new patient</div>
                </a>
                
                <a href="/appointments" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div class="action-title">Book Appointment</div>
                    <div class="action-desc">Schedule consultation</div>
                </a>
                
                <a href="/queue" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-list-ol"></i>
                    </div>
                    <div class="action-title">Manage Queue</div>
                    <div class="action-desc">View waiting list</div>
                </a>
                
                <a href="/medical-records" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <div class="action-title">Medical Records</div>
                    <div class="action-desc">View patient history</div>
                </a>
                
                <a href="/kiosk" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <div class="action-title">Kiosk Mode</div>
                    <div class="action-desc">Self-service terminal</div>
                </a>
                
                <a href="/mobile" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="action-title">Mobile Portal</div>
                    <div class="action-desc">Patient interface</div>
                </a>
            </div>
        </section>
    </main>
    
    <script>
        // Add staggered animation to cards
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('.stat-card');
            const actionCards = document.querySelectorAll('.action-card');
            
            statCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
            
            actionCards.forEach((card, index) => {
                card.style.animationDelay = `${(index * 0.1) + 0.5}s`;
                card.style.animation = 'slideInUp 1s ease-out forwards';
                card.style.opacity = '0';
            });
            
            // Real-time clock
            function updateTime() {
                const now = new Date();
                const timeString = now.toLocaleTimeString();
                // You can add a clock element if needed
            }
            
            setInterval(updateTime, 1000);
        });
    </script>
</body>
</html>
