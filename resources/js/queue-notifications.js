/**
 * Real-time Queue Notification System
 * Provides audio notifications and visual alerts for queue updates
 */

class QueueNotificationSystem {
    constructor() {
        this.audioEnabled = localStorage.getItem('audioEnabled') === 'true';
        this.currentTicket = null;
        this.isInitialized = false;
        this.checkInterval = null;
        
        this.init();
    }
    
    init() {
        this.createAudioElements();
        this.createNotificationPermission();
        this.bindEvents();
        this.startQueueCheck();
        this.isInitialized = true;
        
        console.log('🔔 Queue Notification System initialized');
    }
    
    createAudioElements() {
        // Create audio elements for different notification types
        this.sounds = {
            called: this.createAudio('data:audio/mpeg;base64,SUQzBAAAAAABEVRYWFgAAAAtAAADY29tbWVudABCaWdTb3VuZEJhbmsuY29tIC8gTGFTb25vdGhlcXVlLm9yZwBURU5DAAAAHQAAAW1wM1BST0R1Y2VkIGJ5IEF1ZGFjaXR5AFRJVDIAAAAOAAABbXAzUFJPRHVjZWQAVEFMQgAAABEAAAFhdWRhY2l0eQ=='),
            completed: this.createAudio('data:audio/mpeg;base64,SUQzBAAAAAABEVRYWFgAAAAtAAADY29tbWVudABCaWdTb3VuZEJhbmsuY29tIC8gTGFTb25vdGhlcXVlLm9yZwBURU5DAAAAHQAAAW1wM1BST0R1Y2VkIGJ5IEF1ZGFjaXR5AFRJVDIAAAAOAAABbXAzUFJPRHVjZWQAVEFMQgAAABEAAAFhdWRhY2l0eQ=='),
            emergency: this.createAudio('data:audio/mpeg;base64,SUQzBAAAAAABEVRYWFgAAAAtAAADY29tbWVudABCaWdTb3VuZEJhbmsuY29tIC8gTGFTb25vdGhlcXVlLm9yZwBURU5DAAAAHQAAAW1wM1BST0R1Y2VkIGJ5IEF1ZGFjaXR5AFRJVDIAAAAOAAABbXAzUFJPRHVjZWQAVEFMQgAAABEAAAFhdWRhY2l0eQ==')
        };
    }
    
    createAudio(src) {
        const audio = new Audio();
        audio.preload = 'auto';
        audio.volume = 0.7;
        // For demo purposes, we'll use a simple beep sound
        return audio;
    }
    
    async createNotificationPermission() {
        if ('Notification' in window) {
            const permission = await Notification.requestPermission();
            console.log('🔔 Notification permission:', permission);
        }
    }
    
    bindEvents() {
        // Audio toggle button
        document.addEventListener('click', (e) => {
            if (e.target.id === 'toggleAudio') {
                this.toggleAudio();
            }
        });
        
        // Window focus/blur events
        window.addEventListener('focus', () => {
            this.startQueueCheck();
        });
        
        window.addEventListener('blur', () => {
            // Continue checking but less frequently
            this.startQueueCheck(30000); // 30 seconds when not focused
        });
    }
    
    startQueueCheck(interval = 5000) {
        this.stopQueueCheck();
        
        this.checkInterval = setInterval(() => {
            this.checkQueueUpdates();
        }, interval);
    }
    
    stopQueueCheck() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
            this.checkInterval = null;
        }
    }
    
    async checkQueueUpdates() {
        try {
            // Simulate API call to check queue status
            // In a real system, this would be an actual API endpoint
            const response = await this.simulateQueueAPI();
            
            if (response.hasUpdates) {
                this.handleQueueUpdate(response.data);
            }
        } catch (error) {
            console.error('Queue check failed:', error);
        }
    }
    
    simulateQueueAPI() {
        // Simulate random queue updates for demo
        return new Promise((resolve) => {
            setTimeout(() => {
                const hasUpdate = Math.random() > 0.8; // 20% chance of update
                
                if (hasUpdate) {
                    const updates = [
                        { type: 'called', ticket: 'Q' + String(Math.floor(Math.random() * 100)).padStart(3, '0'), window: Math.floor(Math.random() * 4) + 1 },
                        { type: 'completed', ticket: 'Q' + String(Math.floor(Math.random() * 100)).padStart(3, '0') },
                        { type: 'new', ticket: 'Q' + String(Math.floor(Math.random() * 100)).padStart(3, '0'), priority: Math.floor(Math.random() * 5) }
                    ];
                    
                    resolve({
                        hasUpdates: true,
                        data: updates[Math.floor(Math.random() * updates.length)]
                    });
                } else {
                    resolve({ hasUpdates: false });
                }
            }, 100);
        });
    }
    
    handleQueueUpdate(update) {
        console.log('🔔 Queue update:', update);
        
        switch (update.type) {
            case 'called':
                this.notifyTicketCalled(update.ticket, update.window);
                break;
            case 'completed':
                this.notifyTicketCompleted(update.ticket);
                break;
            case 'new':
                this.notifyNewTicket(update.ticket, update.priority);
                break;
        }
        
        // Update UI
        this.updateQueueDisplay(update);
    }
    
    notifyTicketCalled(ticket, window) {
        const message = `Ticket ${ticket} - Please proceed to Window ${window}`;
        
        // Audio notification
        if (this.audioEnabled) {
            this.playSound('called');
        }
        
        // Browser notification
        this.showNotification('Ticket Called', message, 'info');
        
        // Visual notification in app
        this.showInAppNotification(message, 'info');
        
        // Speech synthesis for accessibility
        this.speak(message);
    }
    
    notifyTicketCompleted(ticket) {
        const message = `Ticket ${ticket} service completed`;
        
        if (this.audioEnabled) {
            this.playSound('completed');
        }
        
        this.showNotification('Service Completed', message, 'success');
        this.showInAppNotification(message, 'success');
    }
    
    notifyNewTicket(ticket, priority) {
        const priorityNames = ['Emergency', 'Faculty', 'Personnel', 'Senior Citizen', 'Regular'];
        const message = `New ${priorityNames[priority]} ticket: ${ticket}`;
        
        if (priority === 0) { // Emergency
            if (this.audioEnabled) {
                this.playSound('emergency');
            }
            this.showNotification('Emergency Ticket', message, 'error');
        }
        
        this.showInAppNotification(message, priority === 0 ? 'error' : 'info');
    }
    
    playSound(type) {
        try {
            // Create a simple beep sound using Web Audio API
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            const frequencies = {
                called: [523, 659], // C5, E5
                completed: [659, 523], // E5, C5
                emergency: [523, 523, 523] // C5 repeated
            };
            
            const freq = frequencies[type] || [523];
            let time = audioContext.currentTime;
            
            freq.forEach((f, i) => {
                oscillator.frequency.setValueAtTime(f, time + i * 0.2);
                gainNode.gain.setTargetAtTime(0.3, time + i * 0.2, 0.01);
                gainNode.gain.setTargetAtTime(0, time + i * 0.2 + 0.15, 0.01);
            });
            
            oscillator.start(time);
            oscillator.stop(time + freq.length * 0.2);
            
        } catch (error) {
            console.warn('Audio playback failed:', error);
        }
    }
    
    showNotification(title, message, type = 'info') {
        if ('Notification' in window && Notification.permission === 'granted') {
            const notification = new Notification(title, {
                body: message,
                icon: '/favicon.ico',
                badge: '/favicon.ico',
                tag: 'queue-update',
                renotify: true
            });
            
            setTimeout(() => notification.close(), 5000);
        }
    }
    
    showInAppNotification(message, type = 'info') {
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
                <i class="fas fa-bell mr-2"></i>
                <span>${message}</span>
                <button class="ml-4 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }
    
    speak(message) {
        if ('speechSynthesis' in window) {
            const utterance = new SpeechSynthesisUtterance(message);
            utterance.rate = 0.8;
            utterance.pitch = 1;
            utterance.volume = 0.8;
            speechSynthesis.speak(utterance);
        }
    }
    
    updateQueueDisplay(update) {
        // Update the queue display with new information
        const queueContainer = document.getElementById('queueContainer');
        if (queueContainer) {
            // Add visual update indicator
            queueContainer.classList.add('animate-pulse');
            setTimeout(() => {
                queueContainer.classList.remove('animate-pulse');
            }, 1000);
        }
        
        // Update last update timestamp
        const lastUpdate = document.getElementById('lastUpdate');
        if (lastUpdate) {
            lastUpdate.textContent = new Date().toLocaleTimeString();
        }
    }
    
    toggleAudio() {
        this.audioEnabled = !this.audioEnabled;
        localStorage.setItem('audioEnabled', this.audioEnabled);
        
        const button = document.getElementById('toggleAudio');
        if (button) {
            const icon = button.querySelector('i');
            if (this.audioEnabled) {
                icon.className = 'fas fa-volume-up';
                button.title = 'Disable Audio Notifications';
            } else {
                icon.className = 'fas fa-volume-mute';
                button.title = 'Enable Audio Notifications';
            }
        }
        
        this.showInAppNotification(
            `Audio notifications ${this.audioEnabled ? 'enabled' : 'disabled'}`,
            'info'
        );
    }
    
    destroy() {
        this.stopQueueCheck();
        console.log('🔔 Queue Notification System destroyed');
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize on queue-related pages
    if (window.location.pathname.includes('queue') || 
        document.getElementById('queueContainer') ||
        document.body.classList.contains('queue-page')) {
        
        window.queueNotifications = new QueueNotificationSystem();
    }
});

// Export for manual initialization
window.QueueNotificationSystem = QueueNotificationSystem;
