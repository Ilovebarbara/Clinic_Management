<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Self-Service Kiosk - University Clinic</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
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
            width: 400px;
            height: 400px;
            top: -150px;
            right: -150px;
            animation-delay: 0s;
        }

        .floating-orb:nth-child(2) {
            width: 300px;
            height: 300px;
            bottom: -100px;
            left: -100px;
            animation-delay: -7s;
        }

        .floating-orb:nth-child(3) {
            width: 200px;
            height: 200px;
            top: 30%;
            left: 20%;
            animation-delay: -14s;
        }

        .floating-orb:nth-child(4) {
            width: 250px;
            height: 250px;
            top: 60%;
            right: 10%;
            animation-delay: -21s;
        }

        @keyframes floatOrb {
            0%, 100% { transform: translate(0, 0) rotate(0deg) scale(1); }
            25% { transform: translate(40px, -40px) rotate(90deg) scale(1.1); }
            50% { transform: translate(-30px, 30px) rotate(180deg) scale(0.9); }
            75% { transform: translate(-40px, -20px) rotate(270deg) scale(1.05); }
        }

        /* Kiosk Container */
        .kiosk-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1000px;
            margin: 2rem;
        }

        /* Welcome Header */
        .welcome-section {
            text-align: center;
            margin-bottom: 3rem;
            animation: slideInUp 0.8s ease;
        }

        .clinic-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .clinic-logo i {
            font-size: 3rem;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: pulse 2s infinite;
        }

        .clinic-name {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #333 0%, #666 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-title {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #333 0%, #666 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }

        .welcome-subtitle {
            font-size: 1.3rem;
            color: #666;
            margin-bottom: 2rem;
        }

        .current-time {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 1rem 2rem;
            display: inline-block;
            font-size: 1.1rem;
            font-weight: 600;
            color: #F79489;
        }

        /* Services Grid */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .service-card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
            padding: 3rem 2rem;
            text-align: center;
            transition: all 0.4s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.8s ease;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
        }

        .service-card:hover {
            transform: translateY(-15px) scale(1.05);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.3);
        }

        .service-card:nth-child(1) { animation-delay: 0.2s; }
        .service-card:nth-child(2) { animation-delay: 0.4s; }
        .service-card:nth-child(3) { animation-delay: 0.6s; }
        .service-card:nth-child(4) { animation-delay: 0.8s; }

        .service-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 2rem;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            box-shadow: 0 10px 30px rgba(247, 148, 137, 0.3);
            transition: all 0.3s ease;
        }

        .service-card:hover .service-icon {
            transform: scale(1.1);
            box-shadow: 0 15px 40px rgba(247, 148, 137, 0.4);
        }

        .service-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
        }

        .service-description {
            color: #666;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .service-action {
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            color: white;
            border: none;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
        }

        .service-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(247, 148, 137, 0.4);
        }

        /* Queue Status */
        .queue-status {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            animation: slideInUp 0.8s ease 1s both;
        }

        .queue-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }

        .queue-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }

        .queue-stat {
            background: rgba(247, 148, 137, 0.1);
            padding: 1.5rem;
            border-radius: 15px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #F79489;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #666;
            font-weight: 500;
            font-size: 0.9rem;
        }

        /* Footer */
        .kiosk-footer {
            margin-top: 3rem;
            text-align: center;
            color: #666;
            animation: slideInUp 0.8s ease 1.2s both;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 1rem;
        }

        .footer-link {
            color: #F79489;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .footer-link:hover {
            color: #333;
            transform: translateY(-2px);
        }

        /* Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
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

        /* Special Effects */
        .shimmer {
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.2) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .kiosk-container {
                margin: 1rem;
            }

            .welcome-title {
                font-size: 2rem;
            }

            .clinic-name {
                font-size: 1.8rem;
            }

            .services-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .service-card {
                padding: 2rem 1.5rem;
            }

            .footer-links {
                flex-direction: column;
                gap: 1rem;
            }
        }

        /* Accessibility */
        .service-card:focus {
            outline: 3px solid #F79489;
            outline-offset: 5px;
        }

        .service-action:focus {
            outline: 2px solid white;
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    <!-- Floating Background Orbs -->
    <div class="floating-orb"></div>
    <div class="floating-orb"></div>
    <div class="floating-orb"></div>
    <div class="floating-orb"></div>

    <!-- Kiosk Container -->
    <div class="kiosk-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="clinic-logo">
                <i class="fas fa-heartbeat"></i>
                <h1 class="clinic-name">University Clinic</h1>
            </div>
            
            <h2 class="welcome-title">Welcome to Our Self-Service Kiosk</h2>
            <p class="welcome-subtitle">Please select the service you need today</p>
            
            <div class="current-time" id="currentTime">
                <i class="fas fa-clock"></i>
                <span id="timeDisplay"></span>
            </div>
        </div>

        <!-- Services Grid -->
        <div class="services-grid">
            <div class="service-card" onclick="selectService('walk-in')" tabindex="0">
                <div class="service-icon">
                    <i class="fas fa-walking"></i>
                </div>
                <h3 class="service-title">Walk-in Consultation</h3>
                <p class="service-description">
                    Get immediate medical attention for non-emergency health concerns. 
                    Our doctors are ready to help you today.
                </p>
                <button class="service-action">
                    <i class="fas fa-plus-circle"></i>
                    Get Queue Number
                </button>
            </div>

            <div class="service-card" onclick="selectService('appointment')" tabindex="0">
                <div class="service-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3 class="service-title">Check-in for Appointment</h3>
                <p class="service-description">
                    Already have an appointment? Check-in here to confirm your arrival 
                    and join the queue.
                </p>
                <button class="service-action">
                    <i class="fas fa-check-circle"></i>
                    Check-in Now
                </button>
            </div>

            <div class="service-card" onclick="selectService('certificate')" tabindex="0">
                <div class="service-icon">
                    <i class="fas fa-certificate"></i>
                </div>
                <h3 class="service-title">Medical Certificate</h3>
                <p class="service-description">
                    Request medical certificates for work, travel, or academic purposes. 
                    Quick and efficient service.
                </p>
                <button class="service-action">
                    <i class="fas fa-file-medical"></i>
                    Request Certificate
                </button>
            </div>

            <div class="service-card" onclick="selectService('emergency')" tabindex="0">
                <div class="service-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="service-title">Emergency Services</h3>
                <p class="service-description">
                    For urgent medical emergencies requiring immediate attention. 
                    Priority access to healthcare.
                </p>
                <button class="service-action" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);">
                    <i class="fas fa-ambulance"></i>
                    Emergency Alert
                </button>
            </div>
        </div>

        <!-- Queue Status -->
        <div class="queue-status">
            <h3 class="queue-title">Current Queue Status</h3>
            <div class="queue-stats">
                <div class="queue-stat">
                    <div class="stat-number" id="waitingCount">12</div>
                    <div class="stat-label">Patients Waiting</div>
                </div>
                <div class="queue-stat">
                    <div class="stat-number" id="averageWait">25</div>
                    <div class="stat-label">Avg. Wait (mins)</div>
                </div>
                <div class="queue-stat">
                    <div class="stat-number" id="activeWindows">3</div>
                    <div class="stat-label">Active Windows</div>
                </div>
                <div class="queue-stat">
                    <div class="stat-number" id="currentServing">Q047</div>
                    <div class="stat-label">Now Serving</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="kiosk-footer">
            <div class="footer-links">
                <a href="#" class="footer-link" onclick="showInfo('hours')">
                    <i class="fas fa-clock"></i> Operating Hours
                </a>
                <a href="#" class="footer-link" onclick="showInfo('contact')">
                    <i class="fas fa-phone"></i> Contact Us
                </a>
                <a href="#" class="footer-link" onclick="showInfo('location')">
                    <i class="fas fa-map-marker-alt"></i> Directions
                </a>
                <a href="#" class="footer-link" onclick="showInfo('help')">
                    <i class="fas fa-question-circle"></i> Help
                </a>
            </div>
            <p>&copy; 2024 University Clinic Management System. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('timeDisplay').textContent = timeString;
        }

        // Service selection
        function selectService(serviceType) {
            const services = {
                'walk-in': {
                    title: 'Walk-in Consultation',
                    message: 'Generating your queue number...',
                    action: () => generateQueueNumber()
                },
                'appointment': {
                    title: 'Appointment Check-in',
                    message: 'Please enter your appointment details...',
                    action: () => showAppointmentForm()
                },
                'certificate': {
                    title: 'Medical Certificate',
                    message: 'Redirecting to certificate request form...',
                    action: () => showCertificateForm()
                },
                'emergency': {
                    title: 'Emergency Services',
                    message: 'Alerting medical staff immediately...',
                    action: () => triggerEmergencyAlert()
                }
            };

            const service = services[serviceType];
            if (service) {
                // Add visual feedback
                event.target.closest('.service-card').style.transform = 'scale(0.95)';
                setTimeout(() => {
                    event.target.closest('.service-card').style.transform = '';
                    alert(`${service.title}\n\n${service.message}`);
                    service.action();
                }, 150);
            }
        }

        // Service actions
        function generateQueueNumber() {
            const queueNumber = 'Q' + String(Math.floor(Math.random() * 900) + 100);
            alert(`Your queue number is: ${queueNumber}\n\nEstimated wait time: 15-20 minutes\n\nPlease wait for your number to be called.`);
            updateQueueStats();
        }

        function showAppointmentForm() {
            const appointmentId = prompt('Please enter your Appointment ID:');
            if (appointmentId) {
                alert(`Thank you! You have been checked in for appointment ${appointmentId}.\n\nPlease have a seat and wait to be called.`);
            }
        }

        function showCertificateForm() {
            alert('Certificate Request Form\n\nPlease proceed to Window 2 with:\n- Valid ID\n- Purpose of certificate\n- Required documents');
        }

        function triggerEmergencyAlert() {
            alert('🚨 EMERGENCY ALERT ACTIVATED 🚨\n\nMedical staff have been notified.\nPlease proceed to Emergency Window immediately.\n\nIf life-threatening, call 911.');
        }

        // Information functions
        function showInfo(type) {
            const info = {
                'hours': 'Operating Hours:\n\nMonday - Friday: 8:00 AM - 6:00 PM\nSaturday: 9:00 AM - 4:00 PM\nSunday: 10:00 AM - 2:00 PM\n\nEmergency services available 24/7',
                'contact': 'Contact Information:\n\nPhone: (555) 123-4567\nEmail: info@universityclinic.edu\nFax: (555) 123-4568\n\nEmergency: 911',
                'location': 'Location:\n\nUniversity Health Center\n123 Campus Drive, Building A\nRoom 101-105\n\nParking available in Lot C',
                'help': 'Need Help?\n\n• Touch any service button to get started\n• For technical issues, press the help button\n• Staff assistance available at reception\n• Emergency button for urgent medical needs'
            };

            alert(info[type] || 'Information not available');
        }

        // Update queue statistics
        function updateQueueStats() {
            const waitingCount = document.getElementById('waitingCount');
            const currentCount = parseInt(waitingCount.textContent);
            waitingCount.textContent = currentCount + 1;
        }

        // Auto-refresh queue stats
        function refreshQueueStats() {
            // Simulate real-time updates
            const stats = {
                waiting: Math.floor(Math.random() * 20) + 5,
                avgWait: Math.floor(Math.random() * 30) + 15,
                activeWindows: Math.floor(Math.random() * 2) + 2,
                currentServing: 'Q' + String(Math.floor(Math.random() * 100) + 1).padStart(3, '0')
            };

            document.getElementById('waitingCount').textContent = stats.waiting;
            document.getElementById('averageWait').textContent = stats.avgWait;
            document.getElementById('activeWindows').textContent = stats.activeWindows;
            document.getElementById('currentServing').textContent = stats.currentServing;
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            const cards = document.querySelectorAll('.service-card');
            const currentFocus = document.activeElement;
            const currentIndex = Array.from(cards).indexOf(currentFocus);

            switch(e.key) {
                case 'ArrowRight':
                case 'ArrowDown':
                    e.preventDefault();
                    const nextIndex = (currentIndex + 1) % cards.length;
                    cards[nextIndex].focus();
                    break;
                case 'ArrowLeft':
                case 'ArrowUp':
                    e.preventDefault();
                    const prevIndex = currentIndex === -1 ? 0 : (currentIndex - 1 + cards.length) % cards.length;
                    cards[prevIndex].focus();
                    break;
                case 'Enter':
                case ' ':
                    if (currentFocus.classList.contains('service-card')) {
                        e.preventDefault();
                        currentFocus.click();
                    }
                    break;
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateTime();
            setInterval(updateTime, 1000);
            setInterval(refreshQueueStats, 30000); // Refresh every 30 seconds

            // Add shimmer effect to service cards
            const cards = document.querySelectorAll('.service-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.position = 'relative';
                    card.style.overflow = 'hidden';
                    
                    const shimmer = document.createElement('div');
                    shimmer.className = 'shimmer';
                    shimmer.style.position = 'absolute';
                    shimmer.style.top = '0';
                    shimmer.style.left = '0';
                    shimmer.style.width = '100%';
                    shimmer.style.height = '100%';
                    shimmer.style.pointerEvents = 'none';
                    
                    card.appendChild(shimmer);
                }, index * 200);
            });

            // Focus first card for keyboard navigation
            if (cards.length > 0) {
                cards[0].focus();
            }
        });
    </script>
</body>
</html>
