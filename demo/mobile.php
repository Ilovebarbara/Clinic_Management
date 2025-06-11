<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Portal - University Clinic</title>
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

        /* Mobile Container */
        .mobile-container {
            max-width: 430px;
            margin: 0 auto;
            min-height: 100vh;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        /* Floating Background Elements */
        .mobile-container::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, rgba(247, 148, 137, 0.1) 0%, rgba(248, 175, 166, 0.15) 100%);
            border-radius: 50%;
            animation: floatOrb 15s infinite ease-in-out;
        }

        .mobile-container::after {
            content: '';
            position: absolute;
            bottom: -30px;
            left: -30px;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(247, 148, 137, 0.08) 0%, rgba(248, 175, 166, 0.12) 100%);
            border-radius: 50%;
            animation: floatOrb 20s infinite ease-in-out reverse;
        }

        @keyframes floatOrb {
            0%, 100% { transform: translate(0, 0) rotate(0deg) scale(1); }
            25% { transform: translate(20px, -20px) rotate(90deg) scale(1.1); }
            50% { transform: translate(-15px, 15px) rotate(180deg) scale(0.9); }
            75% { transform: translate(-20px, -10px) rotate(270deg) scale(1.05); }
        }

        /* Header */
        .mobile-header {
            background: linear-gradient(135deg, rgba(247, 148, 137, 0.2) 0%, rgba(248, 175, 166, 0.3) 100%);
            backdrop-filter: blur(20px);
            padding: 2rem 1.5rem;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        .clinic-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .clinic-logo i {
            font-size: 2rem;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: pulse 2s infinite;
        }

        .clinic-name {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #333 0%, #666 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-message {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 1rem;
        }

        .user-info {
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(15px);
            border-radius: 15px;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .user-details h4 {
            color: #333;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .user-details p {
            color: #666;
            font-size: 0.9rem;
        }

        /* Content */
        .mobile-content {
            padding: 1.5rem;
            position: relative;
            z-index: 10;
        }

        /* Quick Actions */
        .quick-actions {
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .action-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 1.5rem 1rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.8);
        }

        .action-icon {
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
            box-shadow: 0 8px 20px rgba(247, 148, 137, 0.3);
        }

        .action-title {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .action-description {
            font-size: 0.85rem;
            color: #666;
            line-height: 1.4;
        }

        /* Services List */
        .services-section {
            margin-bottom: 2rem;
        }

        .service-item {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .service-item:hover {
            transform: translateX(5px);
            background: rgba(255, 255, 255, 0.8);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .service-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.75rem;
        }

        .service-icon-small {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }

        .service-info h4 {
            color: #333;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .service-info p {
            color: #666;
            font-size: 0.85rem;
        }

        .service-status {
            background: rgba(247, 148, 137, 0.1);
            color: #F79489;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: auto;
        }

        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 430px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 255, 255, 0.3);
            padding: 1rem;
            z-index: 100;
        }

        .nav-items {
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            text-decoration: none;
            color: #666;
            transition: all 0.3s ease;
            padding: 0.5rem;
            border-radius: 12px;
            min-width: 60px;
        }

        .nav-item.active,
        .nav-item:hover {
            color: #F79489;
            background: rgba(247, 148, 137, 0.1);
        }

        .nav-icon {
            font-size: 1.25rem;
        }

        .nav-label {
            font-size: 0.7rem;
            font-weight: 500;
        }

        /* Status Cards */
        .status-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .status-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            padding: 1.25rem;
            text-align: center;
        }

        .status-number {
            font-size: 2rem;
            font-weight: 700;
            color: #F79489;
            margin-bottom: 0.5rem;
        }

        .status-label {
            color: #666;
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Notifications */
        .notification {
            background: linear-gradient(135deg, rgba(247, 148, 137, 0.1) 0%, rgba(248, 175, 166, 0.15) 100%);
            border: 1px solid rgba(247, 148, 137, 0.2);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            background: #F79489;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }

        .notification-content h5 {
            color: #333;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .notification-content p {
            color: #666;
            font-size: 0.8rem;
            line-height: 1.4;
        }

        /* Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
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

        .slide-in {
            animation: slideInUp 0.6s ease;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .mobile-header {
                padding: 1.5rem 1rem;
            }

            .mobile-content {
                padding: 1rem;
                padding-bottom: 5rem;
            }

            .actions-grid {
                gap: 0.75rem;
            }

            .action-card {
                padding: 1.25rem 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <!-- Header -->
        <div class="mobile-header">
            <div class="clinic-logo">
                <i class="fas fa-heartbeat"></i>
                <h1 class="clinic-name">University Clinic</h1>
            </div>
            
            <p class="welcome-message">Mobile Health Portal</p>
            
            <div class="user-info">
                <div class="user-avatar">JD</div>
                <div class="user-details">
                    <h4>John Doe</h4>
                    <p>Student ID: STU-2023-001</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="mobile-content">
            <!-- Quick Actions -->
            <div class="quick-actions slide-in">
                <h3 class="section-title">Quick Actions</h3>
                <div class="actions-grid">
                    <div class="action-card" onclick="quickAction('queue')">
                        <div class="action-icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div class="action-title">Get Queue Number</div>
                        <div class="action-description">Join the walk-in queue</div>
                    </div>

                    <div class="action-card" onclick="quickAction('appointment')">
                        <div class="action-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="action-title">Book Appointment</div>
                        <div class="action-description">Schedule your visit</div>
                    </div>

                    <div class="action-card" onclick="quickAction('checkin')">
                        <div class="action-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="action-title">Check-in</div>
                        <div class="action-description">Confirm your arrival</div>
                    </div>

                    <div class="action-card" onclick="quickAction('records')">
                        <div class="action-icon">
                            <i class="fas fa-file-medical"></i>
                        </div>
                        <div class="action-title">Medical Records</div>
                        <div class="action-description">View your history</div>
                    </div>
                </div>
            </div>

            <!-- Status Cards -->
            <div class="status-cards slide-in">
                <div class="status-card">
                    <div class="status-number" id="queuePosition">--</div>
                    <div class="status-label">Queue Position</div>
                </div>
                <div class="status-card">
                    <div class="status-number" id="waitTime">--</div>
                    <div class="status-label">Wait Time (min)</div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="notification slide-in">
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="notification-content">
                    <h5>Appointment Reminder</h5>
                    <p>Your appointment is scheduled for tomorrow at 2:00 PM with Dr. Smith</p>
                </div>
            </div>

            <!-- Services -->
            <div class="services-section slide-in">
                <h3 class="section-title">Available Services</h3>
                
                <div class="service-item" onclick="selectService('consultation')">
                    <div class="service-header">
                        <div class="service-icon-small">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <div class="service-info">
                            <h4>General Consultation</h4>
                            <p>Basic health check-up and consultation</p>
                        </div>
                        <div class="service-status">Available</div>
                    </div>
                </div>

                <div class="service-item" onclick="selectService('certificate')">
                    <div class="service-header">
                        <div class="service-icon-small">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="service-info">
                            <h4>Medical Certificate</h4>
                            <p>Health certificates for various purposes</p>
                        </div>
                        <div class="service-status">Available</div>
                    </div>
                </div>

                <div class="service-item" onclick="selectService('prescription')">
                    <div class="service-header">
                        <div class="service-icon-small">
                            <i class="fas fa-pills"></i>
                        </div>
                        <div class="service-info">
                            <h4>Prescription Refill</h4>
                            <p>Renew your existing prescriptions</p>
                        </div>
                        <div class="service-status">Available</div>
                    </div>
                </div>

                <div class="service-item" onclick="selectService('emergency')">
                    <div class="service-header">
                        <div class="service-icon-small">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="service-info">
                            <h4>Emergency Services</h4>
                            <p>Urgent medical care when you need it</p>
                        </div>
                        <div class="service-status" style="background: rgba(220, 38, 38, 0.1); color: #dc2626;">24/7</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Navigation -->
        <div class="bottom-nav">
            <div class="nav-items">
                <a href="#" class="nav-item active" onclick="switchTab('home')">
                    <div class="nav-icon"><i class="fas fa-home"></i></div>
                    <div class="nav-label">Home</div>
                </a>
                <a href="#" class="nav-item" onclick="switchTab('appointments')">
                    <div class="nav-icon"><i class="fas fa-calendar"></i></div>
                    <div class="nav-label">Appointments</div>
                </a>
                <a href="#" class="nav-item" onclick="switchTab('queue')">
                    <div class="nav-icon"><i class="fas fa-list"></i></div>
                    <div class="nav-label">Queue</div>
                </a>
                <a href="#" class="nav-item" onclick="switchTab('records')">
                    <div class="nav-icon"><i class="fas fa-file-medical-alt"></i></div>
                    <div class="nav-label">Records</div>
                </a>
                <a href="#" class="nav-item" onclick="switchTab('profile')">
                    <div class="nav-icon"><i class="fas fa-user"></i></div>
                    <div class="nav-label">Profile</div>
                </a>
            </div>
        </div>
    </div>

    <script>
        // Quick actions
        function quickAction(action) {
            const actions = {
                'queue': () => {
                    const queueNumber = 'Q' + String(Math.floor(Math.random() * 900) + 100);
                    alert(`Queue Number Generated!\n\nYour number: ${queueNumber}\nEstimated wait: 15-20 minutes\n\nYou will receive a notification when it's your turn.`);
                    updateQueueStatus(Math.floor(Math.random() * 15) + 1, Math.floor(Math.random() * 25) + 10);
                },
                'appointment': () => {
                    alert('Book Appointment\n\nOpening appointment booking form...\n\nAvailable slots:\n• Today 3:00 PM\n• Tomorrow 10:00 AM\n• Tomorrow 2:00 PM');
                },
                'checkin': () => {
                    alert('Check-in Successful!\n\nYou have been checked in for your appointment.\n\nPlease proceed to the waiting area.');
                },
                'records': () => {
                    alert('Medical Records\n\nAccessing your medical history...\n\nLast visit: June 10, 2024\nNext appointment: June 17, 2024');
                }
            };

            if (actions[action]) {
                // Add haptic feedback simulation
                navigator.vibrate && navigator.vibrate(50);
                actions[action]();
            }
        }

        // Service selection
        function selectService(service) {
            const services = {
                'consultation': 'General Consultation selected.\n\nThis service includes basic health assessment, symptom evaluation, and medical advice.',
                'certificate': 'Medical Certificate requested.\n\nAvailable certificates:\n• Fitness certificate\n• Health clearance\n• Travel medical certificate',
                'prescription': 'Prescription Refill requested.\n\nPlease bring your current prescription and ID for verification.',
                'emergency': '🚨 Emergency Services\n\nFor life-threatening emergencies, please call 911 immediately.\n\nFor urgent care, proceed to emergency window.'
            };

            alert(services[service] || 'Service information not available');
        }

        // Navigation
        function switchTab(tab) {
            // Remove active class from all nav items
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });

            // Add active class to clicked item
            event.target.closest('.nav-item').classList.add('active');

            // Tab switching logic
            const tabs = {
                'home': () => {
                    alert('Home - Current page');
                },
                'appointments': () => {
                    alert('Appointments\n\nUpcoming appointments:\n• June 17, 2024 - 2:00 PM\n  Dr. Smith - Follow-up\n\n• June 24, 2024 - 10:00 AM\n  Dr. Johnson - Routine check');
                },
                'queue': () => {
                    alert('Queue Status\n\nCurrent queue information:\n• Position: 5\n• Estimated wait: 12 minutes\n• Now serving: Q087');
                },
                'records': () => {
                    alert('Medical Records\n\nRecent visits:\n• June 10, 2024 - General consultation\n• May 15, 2024 - Health screening\n• April 8, 2024 - Vaccination');
                },
                'profile': () => {
                    alert('Profile\n\nJohn Doe\nStudent ID: STU-2023-001\nPhone: (555) 123-4567\nEmail: john.doe@university.edu\n\nEmergency Contact:\nJane Doe - (555) 987-6543');
                }
            };

            if (tabs[tab]) {
                tabs[tab]();
            }
        }

        // Update queue status
        function updateQueueStatus(position, waitTime) {
            document.getElementById('queuePosition').textContent = position;
            document.getElementById('waitTime').textContent = waitTime;
        }

        // Simulate real-time updates
        function simulateUpdates() {
            const position = Math.max(1, Math.floor(Math.random() * 20));
            const waitTime = Math.floor(Math.random() * 30) + 5;
            updateQueueStatus(position, waitTime);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Add slide-in animation to elements
            const slideElements = document.querySelectorAll('.slide-in');
            slideElements.forEach((element, index) => {
                element.style.animationDelay = `${index * 0.2}s`;
            });

            // Simulate initial queue status
            setTimeout(() => {
                simulateUpdates();
            }, 1000);

            // Update queue status periodically
            setInterval(simulateUpdates, 30000);

            // Add touch feedback for action cards
            const actionCards = document.querySelectorAll('.action-card, .service-item');
            actionCards.forEach(card => {
                card.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                });
                
                card.addEventListener('touchend', function() {
                    this.style.transform = '';
                });
            });

            // Prevent double-tap zoom on buttons
            let lastTouchEnd = 0;
            document.addEventListener('touchend', function(event) {
                const now = (new Date()).getTime();
                if (now - lastTouchEnd <= 300) {
                    event.preventDefault();
                }
                lastTouchEnd = now;
            }, false);
        });

        // Service Worker for offline functionality (basic registration)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful');
                    })
                    .catch(function(error) {
                        console.log('ServiceWorker registration failed');
                    });
            });
        }
    </script>
</body>
</html>
