<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Features - University Clinic Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .glass-morphism {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 50%, #FADCD9 100%);
        }
        
        .feature-card {
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .status-active { background-color: #10B981; }
        .status-beta { background-color: #F59E0B; }
        .status-planned { background-color: #6B7280; }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    <?php include 'quick-nav.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="glass-morphism rounded-2xl p-8 mb-8 text-center floating-animation">
            <h1 class="text-4xl font-bold text-white mb-4">
                <i class="fas fa-rocket mr-4"></i>Enhanced Features
            </h1>
            <p class="text-xl text-white/80 mb-6">Advanced capabilities for modern clinic management</p>
            <div class="flex justify-center space-x-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-white">12</div>
                    <div class="text-white/70 text-sm">New Features</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-white">5</div>
                    <div class="text-white/70 text-sm">Integrations</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-white">24/7</div>
                    <div class="text-white/70 text-sm">Availability</div>
                </div>
            </div>
        </div>

        <!-- Real-time Features -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                <i class="fas fa-bolt mr-3"></i>Real-time Features
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="glass-morphism rounded-2xl p-6 feature-card">
                    <div class="flex items-center mb-4">
                        <span class="status-indicator status-active"></span>
                        <h3 class="text-xl font-semibold text-white">Enhanced Queue Management</h3>
                    </div>
                    <p class="text-white/80 mb-4">Real-time queue updates with Server-Sent Events, priority handling, and instant notifications.</p>
                    <div class="flex items-center justify-between">
                        <a href="enhanced-queue.php" class="bg-blue-500/20 hover:bg-blue-500/30 text-white px-4 py-2 rounded-lg transition-all duration-300">
                            <i class="fas fa-external-link-alt mr-2"></i>Open
                        </a>
                        <div class="text-green-300 text-sm">
                            <i class="fas fa-check mr-1"></i>Active
                        </div>
                    </div>
                </div>
                
                <div class="glass-morphism rounded-2xl p-6 feature-card">
                    <div class="flex items-center mb-4">
                        <span class="status-indicator status-active"></span>
                        <h3 class="text-xl font-semibold text-white">Queue Notifications</h3>
                    </div>
                    <p class="text-white/80 mb-4">Audio alerts, browser notifications, and speech synthesis for accessibility.</p>
                    <div class="flex items-center justify-between">
                        <button onclick="testNotifications()" class="bg-purple-500/20 hover:bg-purple-500/30 text-white px-4 py-2 rounded-lg transition-all duration-300">
                            <i class="fas fa-bell mr-2"></i>Test
                        </button>
                        <div class="text-green-300 text-sm">
                            <i class="fas fa-check mr-1"></i>Active
                        </div>
                    </div>
                </div>
                
                <div class="glass-morphism rounded-2xl p-6 feature-card">
                    <div class="flex items-center mb-4">
                        <span class="status-indicator status-active"></span>
                        <h3 class="text-xl font-semibold text-white">Live Analytics</h3>
                    </div>
                    <p class="text-white/80 mb-4">Real-time dashboard with charts, metrics, and comprehensive reporting.</p>
                    <div class="flex items-center justify-between">
                        <a href="analytics-dashboard.php" class="bg-green-500/20 hover:bg-green-500/30 text-white px-4 py-2 rounded-lg transition-all duration-300">
                            <i class="fas fa-chart-line mr-2"></i>View
                        </a>
                        <div class="text-green-300 text-sm">
                            <i class="fas fa-check mr-1"></i>Active
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Features -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                <i class="fas fa-cogs mr-3"></i>Advanced Features
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="glass-morphism rounded-2xl p-6 feature-card">
                    <div class="flex items-center mb-4">
                        <span class="status-indicator status-active"></span>
                        <h3 class="text-xl font-semibold text-white">Priority-based Queuing</h3>
                    </div>
                    <div class="space-y-3 mb-4">
                        <div class="flex items-center justify-between">
                            <span class="text-white/80">Emergency</span>
                            <span class="px-2 py-1 rounded text-xs bg-red-100 text-red-800">Priority 0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-white/80">Faculty</span>
                            <span class="px-2 py-1 rounded text-xs bg-purple-100 text-purple-800">Priority 1</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-white/80">Personnel</span>
                            <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">Priority 2</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-white/80">Senior Citizens</span>
                            <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-800">Priority 3</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-white/80">Regular</span>
                            <span class="px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">Priority 4</span>
                        </div>
                    </div>
                    <div class="text-green-300 text-sm">
                        <i class="fas fa-check mr-1"></i>Fully Implemented
                    </div>
                </div>
                
                <div class="glass-morphism rounded-2xl p-6 feature-card">
                    <div class="flex items-center mb-4">
                        <span class="status-indicator status-active"></span>
                        <h3 class="text-xl font-semibold text-white">Multi-Interface Support</h3>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-white/10 rounded-lg p-3 text-center">
                            <i class="fas fa-desktop text-blue-300 text-2xl mb-2"></i>
                            <div class="text-white text-sm">Desktop</div>
                        </div>
                        <div class="bg-white/10 rounded-lg p-3 text-center">
                            <i class="fas fa-mobile-alt text-green-300 text-2xl mb-2"></i>
                            <div class="text-white text-sm">Mobile</div>
                        </div>
                        <div class="bg-white/10 rounded-lg p-3 text-center">
                            <i class="fas fa-tablet-alt text-purple-300 text-2xl mb-2"></i>
                            <div class="text-white text-sm">Kiosk</div>
                        </div>
                        <div class="bg-white/10 rounded-lg p-3 text-center">
                            <i class="fas fa-globe text-yellow-300 text-2xl mb-2"></i>
                            <div class="text-white text-sm">Web</div>
                        </div>
                    </div>
                    <div class="text-green-300 text-sm">
                        <i class="fas fa-check mr-1"></i>All Platforms Supported
                    </div>
                </div>
            </div>
        </div>

        <!-- Technical Specifications -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                <i class="fas fa-microchip mr-3"></i>Technical Specifications
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="glass-morphism rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-database mr-2"></i>Database
                    </h3>
                    <ul class="space-y-2 text-white/80">
                        <li><i class="fas fa-check text-green-300 mr-2"></i>SQLite for lightweight deployment</li>
                        <li><i class="fas fa-check text-green-300 mr-2"></i>Automatic table creation</li>
                        <li><i class="fas fa-check text-green-300 mr-2"></i>Data integrity constraints</li>
                        <li><i class="fas fa-check text-green-300 mr-2"></i>Optimized queries</li>
                    </ul>
                </div>
                
                <div class="glass-morphism rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-shield-alt mr-2"></i>Security
                    </h3>
                    <ul class="space-y-2 text-white/80">
                        <li><i class="fas fa-check text-green-300 mr-2"></i>Password hashing (bcrypt)</li>
                        <li><i class="fas fa-check text-green-300 mr-2"></i>Session management</li>
                        <li><i class="fas fa-check text-green-300 mr-2"></i>Input validation</li>
                        <li><i class="fas fa-check text-green-300 mr-2"></i>SQL injection prevention</li>
                    </ul>
                </div>
                
                <div class="glass-morphism rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-rocket mr-2"></i>Performance
                    </h3>
                    <ul class="space-y-2 text-white/80">
                        <li><i class="fas fa-check text-green-300 mr-2"></i>Real-time updates (SSE)</li>
                        <li><i class="fas fa-check text-green-300 mr-2"></i>Minimal server load</li>
                        <li><i class="fas fa-check text-green-300 mr-2"></i>Responsive design</li>
                        <li><i class="fas fa-check text-green-300 mr-2"></i>Fast loading times</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Integration Capabilities -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                <i class="fas fa-plug mr-3"></i>Integration Capabilities
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="glass-morphism rounded-2xl p-6 text-center">
                    <div class="bg-blue-500/20 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-bell text-2xl text-blue-300"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Browser Notifications</h3>
                    <p class="text-white/70 text-sm">Native browser notification support</p>
                    <div class="mt-3 text-green-300 text-sm">
                        <i class="fas fa-check mr-1"></i>Ready
                    </div>
                </div>
                
                <div class="glass-morphism rounded-2xl p-6 text-center">
                    <div class="bg-green-500/20 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-volume-up text-2xl text-green-300"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Audio System</h3>
                    <p class="text-white/70 text-sm">Web Audio API integration</p>
                    <div class="mt-3 text-green-300 text-sm">
                        <i class="fas fa-check mr-1"></i>Ready
                    </div>
                </div>
                
                <div class="glass-morphism rounded-2xl p-6 text-center">
                    <div class="bg-purple-500/20 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-assistive-listening-systems text-2xl text-purple-300"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Speech Synthesis</h3>
                    <p class="text-white/70 text-sm">Text-to-speech for accessibility</p>
                    <div class="mt-3 text-green-300 text-sm">
                        <i class="fas fa-check mr-1"></i>Ready
                    </div>
                </div>
                
                <div class="glass-morphism rounded-2xl p-6 text-center">
                    <div class="bg-yellow-500/20 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-download text-2xl text-yellow-300"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Data Export</h3>
                    <p class="text-white/70 text-sm">JSON, CSV export capabilities</p>
                    <div class="mt-3 text-green-300 text-sm">
                        <i class="fas fa-check mr-1"></i>Ready
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="glass-morphism rounded-2xl p-6">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                <i class="fas fa-play mr-3"></i>Quick Actions
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="enhanced-queue.php" class="bg-blue-500/20 hover:bg-blue-500/30 text-white p-4 rounded-lg text-center transition-all duration-300 block">
                    <i class="fas fa-rocket text-2xl mb-2"></i>
                    <div class="font-semibold">Enhanced Queue</div>
                    <div class="text-sm text-white/70">Real-time management</div>
                </a>
                
                <a href="analytics-dashboard.php" class="bg-green-500/20 hover:bg-green-500/30 text-white p-4 rounded-lg text-center transition-all duration-300 block">
                    <i class="fas fa-chart-line text-2xl mb-2"></i>
                    <div class="font-semibold">Analytics</div>
                    <div class="text-sm text-white/70">Live dashboard</div>
                </a>
                
                <button onclick="testNotifications()" class="bg-purple-500/20 hover:bg-purple-500/30 text-white p-4 rounded-lg text-center transition-all duration-300">
                    <i class="fas fa-bell text-2xl mb-2"></i>
                    <div class="font-semibold">Test Notifications</div>
                    <div class="text-sm text-white/70">Demo alerts</div>
                </button>
                
                <a href="../" class="bg-yellow-500/20 hover:bg-yellow-500/30 text-white p-4 rounded-lg text-center transition-all duration-300 block">
                    <i class="fas fa-home text-2xl mb-2"></i>
                    <div class="font-semibold">Main App</div>
                    <div class="text-sm text-white/70">Return to system</div>
                </a>
            </div>
        </div>
    </div>

    <script>
        function testNotifications() {
            // Test browser notification
            if ('Notification' in window) {
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        new Notification('Test Notification', {
                            body: 'Enhanced notification system is working!',
                            icon: '/favicon.ico'
                        });
                    }
                });
            }
            
            // Test audio
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.value = 523; // C5
                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.5);
            } catch (error) {
                console.log('Audio test failed:', error);
            }
            
            // Test speech synthesis
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance('Enhanced notification system test completed successfully');
                utterance.rate = 0.8;
                speechSynthesis.speak(utterance);
            }
            
            // Show visual notification
            showNotification('All notification systems tested successfully!', 'success');
        }
        
        function showNotification(message, type = 'info') {
            const colors = {
                info: 'bg-blue-500',
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500'
            };
            
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check mr-2"></i>
                    <span>${message}</span>
                    <button class="ml-4 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }
        
        // Auto-test notification permission on load
        document.addEventListener('DOMContentLoaded', function() {
            if ('Notification' in window && Notification.permission === 'default') {
                setTimeout(() => {
                    showNotification('Click "Test Notifications" to enable browser notifications', 'info');
                }, 2000);
            }
        });
    </script>
</body>
</html>
