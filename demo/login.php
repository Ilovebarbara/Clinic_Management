<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniClinic - Staff Login | Advanced Healthcare Management</title>
    
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
            position: relative;
        }
        
        /* Animated Background Elements */
        .bg-animated {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            overflow: hidden;
            pointer-events: none;
        }
        
        .floating-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            animation: floatOrb 20s ease-in-out infinite;
        }
        
        .orb-1 {
            top: 15%;
            left: 10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(247,148,137,0.15) 0%, rgba(247,148,137,0.05) 50%, transparent 70%);
            animation-delay: 0s;
        }
        
        .orb-2 {
            top: 60%;
            right: 15%;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(248,175,166,0.12) 0%, rgba(248,175,166,0.04) 40%, transparent 70%);
            animation-delay: 7s;
        }
        
        .orb-3 {
            bottom: 25%;
            left: 20%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(250,220,217,0.18) 0%, rgba(250,220,217,0.06) 45%, transparent 70%);
            animation-delay: 14s;
        }
        
        .geometric-element {
            position: absolute;
            animation: geometricFloat 15s ease-in-out infinite;
        }
        
        .geo-1 {
            top: 20%;
            right: 25%;
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, rgba(247,148,137,0.1), rgba(248,175,166,0.05));
            border: 2px solid rgba(247,148,137,0.2);
            border-radius: 20px;
            transform: rotate(45deg);
        }
        
        .geo-2 {
            bottom: 35%;
            right: 35%;
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, rgba(250,220,217,0.15), rgba(249,241,240,0.08));
            border: 1px solid rgba(250,220,217,0.3);
            border-radius: 50%;
        }
        
        /* Login Container */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(247,148,137,0.1);
            border-radius: 24px;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            animation: cardSlideIn 1s ease-out;
        }
        
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(247,148,137,0.05) 0%, rgba(248,175,166,0.03) 100%);
            border-radius: 24px;
            pointer-events: none;
        }
        
        .card-particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }
        
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: #F79489;
            border-radius: 50%;
            animation: cardParticle 8s ease-in-out infinite;
        }
        
        .particle:nth-child(1) { top: 20%; left: 20%; animation-delay: 0s; }
        .particle:nth-child(2) { top: 40%; right: 15%; animation-delay: 2s; background: #F8AFA6; }
        .particle:nth-child(3) { bottom: 30%; left: 30%; animation-delay: 4s; background: #FADCD9; }
        .particle:nth-child(4) { top: 60%; right: 25%; animation-delay: 6s; }
        
        .logo-section {
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
            z-index: 2;
        }
        
        .logo-icon {
            font-size: 3rem;
            color: #F79489;
            margin-bottom: 1rem;
            filter: drop-shadow(0 0 20px rgba(247,148,137,0.3));
            animation: logoFloat 6s ease-in-out infinite;
        }
        
        .logo-text {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        
        .login-subtitle {
            color: rgba(45,55,72,0.7);
            font-size: 0.95rem;
            font-weight: 500;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .form-input {
            width: 100%;
            padding: 1rem 1.2rem;
            border: 2px solid rgba(247,148,137,0.1);
            border-radius: 16px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #F79489;
            box-shadow: 0 0 0 4px rgba(247,148,137,0.1);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(247,148,137,0.6);
            font-size: 1.1rem;
            z-index: 3;
        }
        
        .login-button {
            width: 100%;
            padding: 1.2rem;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(247,148,137,0.3);
        }
        
        .login-button:hover::before {
            left: 100%;
        }
        
        .login-options {
            text-align: center;
            position: relative;
            z-index: 2;
        }
        
        .demo-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }
        
        .demo-link {
            flex: 1;
            padding: 0.8rem 1rem;
            background: rgba(247,148,137,0.1);
            color: #F79489;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(247,148,137,0.2);
            text-align: center;
        }
        
        .demo-link:hover {
            background: rgba(247,148,137,0.2);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(247,148,137,0.2);
            color: #F79489;
            text-decoration: none;
        }
        
        /* Animations */
        @keyframes floatOrb {
            0%, 100% { transform: translateY(0px) translateX(0px); }
            25% { transform: translateY(-20px) translateX(10px); }
            50% { transform: translateY(-10px) translateX(-15px); }
            75% { transform: translateY(-25px) translateX(5px); }
        }
        
        @keyframes geometricFloat {
            0%, 100% { transform: rotate(45deg) translateY(0px); opacity: 0.6; }
            50% { transform: rotate(225deg) translateY(-15px); opacity: 1; }
        }
        
        @keyframes cardSlideIn {
            0% { 
                opacity: 0; 
                transform: translateY(50px) scale(0.9); 
            }
            100% { 
                opacity: 1; 
                transform: translateY(0px) scale(1); 
            }
        }
        
        @keyframes cardParticle {
            0%, 100% { transform: translateY(0px) scale(1); opacity: 0.6; }
            50% { transform: translateY(-15px) scale(1.2); opacity: 1; }
        }
        
        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .login-card {
                margin: 1rem;
                padding: 2rem;
            }
            
            .logo-icon {
                font-size: 2.5rem;
            }
            
            .logo-text {
                font-size: 1.7rem;
            }
            
            .demo-links {
                flex-direction: column;
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
        <div class="geometric-element geo-1"></div>
        <div class="geometric-element geo-2"></div>
    </div>
    
    <div class="login-container">
        <div class="login-card">
            <!-- Card Particles -->
            <div class="card-particles">
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
            </div>
            
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <h1 class="logo-text">UniClinic</h1>
                <p class="login-subtitle">Advanced Healthcare Management System</p>
            </div>
            
            <!-- Login Form -->
            <form id="loginForm" action="dashboard.php" method="POST">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" 
                           placeholder="Enter your email" value="admin@clinic.edu" required>
                    <i class="fas fa-envelope input-icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" 
                           placeholder="Enter your password" value="password" required>
                    <i class="fas fa-lock input-icon"></i>
                </div>
                
                <button type="submit" class="login-button">
                    <i class="fas fa-sign-in-alt" style="margin-right: 0.5rem;"></i>
                    Sign In to Dashboard
                </button>
            </form>
            
            <!-- Demo Links -->
            <div class="login-options">
                <p style="margin-bottom: 1rem; color: rgba(45,55,72,0.7); font-size: 0.9rem;">
                    Or explore our demo system:
                </p>
                <div class="demo-links">
                    <a href="dashboard.php" class="demo-link">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="patients.php" class="demo-link">
                        <i class="fas fa-users"></i> Patients
                    </a>
                    <a href="appointments.php" class="demo-link">
                        <i class="fas fa-calendar-check"></i> Appointments
                    </a>
                    <a href="queue.php" class="demo-link">
                        <i class="fas fa-list-ol"></i> Queue
                    </a>
                    <a href="kiosk.php" class="demo-link">
                        <i class="fas fa-desktop"></i> Kiosk
                    </a>
                    <a href="mobile.php" class="demo-link">
                        <i class="fas fa-mobile-alt"></i> Mobile
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Form submission with animation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const button = this.querySelector('.login-button');
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 0.5rem;"></i>Signing In...';
            button.style.opacity = '0.8';
            
            // Simulate login delay
            setTimeout(() => {
                window.location.href = 'dashboard.php';
            }, 1500);
        });
        
        // Add input focus effects
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
                this.parentElement.style.transition = 'transform 0.3s ease';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0px)';
            });
        });
    </script>
</body>
</html>
