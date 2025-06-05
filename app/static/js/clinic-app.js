// Real-time Dashboard and Queue Management JavaScript
// Clinic Management System

class ClinicApp {
    constructor() {
        this.updateInterval = 30000; // 30 seconds
        this.timers = new Map();
        this.init();
    }

    init() {
        this.initializeComponents();
        this.setupEventListeners();
        this.startAutoUpdates();
    }

    initializeComponents() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize modals
        this.initializeModals();

        // Initialize form validators
        this.initializeFormValidation();

        // Initialize queue components
        this.initializeQueueComponents();
    }

    initializeModals() {
        // Auto-close alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    }

    initializeFormValidation() {
        // Bootstrap form validation
        const forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });

        // Password strength indicator
        const passwordInputs = document.querySelectorAll('input[type="password"]');
        passwordInputs.forEach(input => {
            input.addEventListener('input', (e) => this.checkPasswordStrength(e.target));
        });
    }

    initializeQueueComponents() {
        // Queue ticket generation
        const ticketForm = document.getElementById('ticketForm');
        if (ticketForm) {
            ticketForm.addEventListener('submit', (e) => this.generateTicket(e));
        }

        // Queue management buttons
        this.setupQueueButtons();

        // Auto-refresh queue status
        if (document.querySelector('.queue-status')) {
            this.startQueueUpdates();
        }
    }

    setupEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('globalSearch');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.performSearch(e.target.value);
                }, 500);
            });
        }

        // Profile picture upload
        const profilePicInput = document.getElementById('profile_picture');
        if (profilePicInput) {
            profilePicInput.addEventListener('change', (e) => this.previewProfilePicture(e));
        }

        // Appointment date/time validation
        const appointmentForm = document.getElementById('appointmentForm');
        if (appointmentForm) {
            this.setupAppointmentValidation(appointmentForm);
        }

        // Real-time notifications
        this.setupNotifications();
    }

    startAutoUpdates() {
        // Dashboard statistics
        if (document.querySelector('.dashboard-stats')) {
            this.updateDashboardStats();
            setInterval(() => this.updateDashboardStats(), this.updateInterval);
        }

        // Notifications
        this.updateNotifications();
        setInterval(() => this.updateNotifications(), 60000); // 1 minute
    }

    startQueueUpdates() {
        this.updateQueueStatus();
        setInterval(() => this.updateQueueStatus(), 10000); // 10 seconds for queue
    }

    // Dashboard Methods
    async updateDashboardStats() {
        try {
            const response = await fetch('/api/dashboard-stats');
            const data = await response.json();
            
            if (data.success) {
                this.updateStatCards(data);
            }
        } catch (error) {
            console.error('Error updating dashboard stats:', error);
        }
    }

    updateStatCards(data) {
        const updates = {
            'total-patients': data.total_patients,
            'todays-appointments': data.todays_appointments,
            'queue-length': data.queue_length,
            'active-staff': data.active_staff
        };

        Object.entries(updates).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                this.animateCounter(element, value);
            }
        });
    }

    animateCounter(element, targetValue) {
        const currentValue = parseInt(element.textContent) || 0;
        const increment = (targetValue - currentValue) / 20;
        let current = currentValue;

        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= targetValue) || 
                (increment < 0 && current <= targetValue)) {
                current = targetValue;
                clearInterval(timer);
            }
            element.textContent = Math.round(current);
        }, 50);
    }

    // Queue Management Methods
    async updateQueueStatus() {
        try {
            const response = await fetch('/api/queue/current-status');
            const data = await response.json();
            
            if (data.success) {
                this.updateQueueDisplay(data);
            }
        } catch (error) {
            console.error('Error updating queue status:', error);
        }
    }

    updateQueueDisplay(data) {
        // Update current serving
        const currentDisplay = document.getElementById('current-serving');
        if (currentDisplay && data.current) {
            currentDisplay.innerHTML = `
                <div class="serving-ticket">
                    <h3>${data.current.number}</h3>
                    <p>${data.current.service}</p>
                    <span class="priority-badge priority-${data.current.priority}">${data.current.priority}</span>
                </div>
            `;
        } else if (currentDisplay) {
            currentDisplay.innerHTML = '<p class="text-muted">No patient currently being served</p>';
        }

        // Update waiting list
        const waitingList = document.getElementById('waiting-list');
        if (waitingList && data.waiting) {
            waitingList.innerHTML = data.waiting.map(ticket => `
                <div class="waiting-ticket">
                    <span class="ticket-number">${ticket.number}</span>
                    <span class="service">${ticket.service}</span>
                    <span class="wait-time">${ticket.wait_time}</span>
                    <span class="priority-badge priority-${ticket.priority}">${ticket.priority}</span>
                </div>
            `).join('');
        }

        // Update statistics
        if (data.stats) {
            this.updateElement('total-waiting', data.stats.total_waiting);
            this.updateElement('average-wait', data.stats.average_wait);
            this.updateElement('served-today', data.stats.served_today);
        }

        // Update timestamp
        this.updateElement('last-updated', new Date().toLocaleTimeString());
    }

    async generateTicket(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const ticketData = {
            service: formData.get('service'),
            priority: formData.get('priority') || 'normal'
        };

        try {
            const response = await fetch('/api/queue/generate-ticket', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(ticketData)
            });

            const data = await response.json();
            
            if (data.success) {
                this.showTicketGenerated(data.ticket);
                event.target.reset();
                
                // Auto-print if enabled
                if (data.ticket.auto_print) {
                    this.printTicket(data.ticket);
                }
            } else {
                this.showAlert('Error generating ticket: ' + data.message, 'danger');
            }
        } catch (error) {
            this.showAlert('Error generating ticket', 'danger');
            console.error('Error:', error);
        }
    }

    showTicketGenerated(ticket) {
        const modal = new bootstrap.Modal(document.getElementById('ticketModal'));
        document.getElementById('ticketNumber').textContent = ticket.number;
        document.getElementById('ticketService').textContent = ticket.service;
        document.getElementById('ticketPriority').textContent = ticket.priority;
        document.getElementById('ticketTime').textContent = new Date(ticket.timestamp).toLocaleString();
        modal.show();
    }

    printTicket(ticket) {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Queue Ticket</title>
                    <style>
                        body { font-family: monospace; text-align: center; padding: 20px; }
                        .ticket { border: 2px dashed #333; padding: 20px; margin: 20px; }
                        .number { font-size: 2em; font-weight: bold; }
                        .qr-code { margin: 10px; }
                    </style>
                </head>
                <body>
                    <div class="ticket">
                        <h2>University Health Center</h2>
                        <div class="number">${ticket.number}</div>
                        <p>Service: ${ticket.service}</p>
                        <p>Priority: ${ticket.priority}</p>
                        <p>Time: ${new Date(ticket.timestamp).toLocaleString()}</p>
                        <div class="qr-code">■■■■■■■■■</div>
                        <p>Please wait for your number to be called</p>
                    </div>
                </body>
            </html>
        `);
        printWindow.document.close();
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    }

    setupQueueButtons() {
        // Call next patient
        const callNextBtn = document.getElementById('callNextBtn');
        if (callNextBtn) {
            callNextBtn.addEventListener('click', () => this.callNextPatient());
        }

        // Complete current patient
        const completeBtn = document.getElementById('completeCurrentBtn');
        if (completeBtn) {
            completeBtn.addEventListener('click', () => this.completeCurrentPatient());
        }

        // Skip current patient
        const skipBtn = document.getElementById('skipCurrentBtn');
        if (skipBtn) {
            skipBtn.addEventListener('click', () => this.skipCurrentPatient());
        }
    }

    async callNextPatient() {
        try {
            const response = await fetch('/api/queue/call-next', { method: 'POST' });
            const data = await response.json();
            
            if (data.success) {
                this.showAlert(`Called patient: ${data.ticket.number}`, 'success');
                this.updateQueueStatus();
            } else {
                this.showAlert(data.message, 'warning');
            }
        } catch (error) {
            this.showAlert('Error calling next patient', 'danger');
        }
    }

    async completeCurrentPatient() {
        try {
            const response = await fetch('/api/queue/complete-current', { method: 'POST' });
            const data = await response.json();
            
            if (data.success) {
                this.showAlert('Patient consultation completed', 'success');
                this.updateQueueStatus();
            } else {
                this.showAlert(data.message, 'warning');
            }
        } catch (error) {
            this.showAlert('Error completing consultation', 'danger');
        }
    }

    async skipCurrentPatient() {
        try {
            const response = await fetch('/api/queue/skip-current', { method: 'POST' });
            const data = await response.json();
            
            if (data.success) {
                this.showAlert('Patient skipped', 'info');
                this.updateQueueStatus();
            } else {
                this.showAlert(data.message, 'warning');
            }
        } catch (error) {
            this.showAlert('Error skipping patient', 'danger');
        }
    }

    // Notification Methods
    async updateNotifications() {
        try {
            const response = await fetch('/api/notifications');
            const data = await response.json();
            
            if (data.success) {
                this.displayNotifications(data.notifications);
            }
        } catch (error) {
            console.error('Error updating notifications:', error);
        }
    }

    displayNotifications(notifications) {
        const container = document.getElementById('notifications-container');
        if (!container) return;

        container.innerHTML = notifications.map(notification => `
            <div class="alert alert-${notification.type} alert-dismissible fade show" role="alert">
                ${notification.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `).join('');
    }

    setupNotifications() {
        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }

    showBrowserNotification(title, message, type = 'info') {
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(title, {
                body: message,
                icon: '/static/img/clinic-icon.png'
            });
        }
    }

    // Search Methods
    async performSearch(query) {
        if (query.length < 2) {
            document.getElementById('search-results').innerHTML = '';
            return;
        }

        try {
            const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            if (data.success) {
                this.displaySearchResults(data.results);
            }
        } catch (error) {
            console.error('Search error:', error);
        }
    }

    displaySearchResults(results) {
        const container = document.getElementById('search-results');
        if (!container) return;

        let html = '';
        
        if (results.patients.length > 0) {
            html += '<h6>Patients</h6>';
            results.patients.forEach(patient => {
                html += `<div class="search-result" onclick="window.location.href='/patients/${patient.id}'">
                    <strong>${patient.name}</strong> - ${patient.patient_number}
                </div>`;
            });
        }

        if (results.doctors.length > 0) {
            html += '<h6>Doctors</h6>';
            results.doctors.forEach(doctor => {
                html += `<div class="search-result" onclick="window.location.href='/doctors/${doctor.id}'">
                    <strong>${doctor.name}</strong> - ${doctor.specialization}
                </div>`;
            });
        }

        container.innerHTML = html;
    }

    // Utility Methods
    updateElement(id, value) {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        }
    }

    showAlert(message, type = 'info') {
        const alertsContainer = document.getElementById('alerts-container') || document.body;
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        alertsContainer.insertBefore(alertDiv, alertsContainer.firstChild);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertDiv);
            bsAlert.close();
        }, 5000);
    }

    checkPasswordStrength(input) {
        const password = input.value;
        const strengthIndicator = input.parentElement.querySelector('.password-strength');
        
        if (!strengthIndicator) return;

        let strength = 0;
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        const levels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
        const colors = ['danger', 'warning', 'warning', 'info', 'success'];
        
        strengthIndicator.textContent = levels[strength] || 'Very Weak';
        strengthIndicator.className = `password-strength text-${colors[strength] || 'danger'}`;
    }

    previewProfilePicture(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('profile-preview');
        
        if (file && preview) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    setupAppointmentValidation(form) {
        const dateInput = form.querySelector('input[type="date"]');
        const timeInput = form.querySelector('input[type="time"]');
        
        if (dateInput) {
            // Set minimum date to today
            dateInput.min = new Date().toISOString().split('T')[0];
        }

        form.addEventListener('submit', (e) => {
            if (dateInput && timeInput) {
                const selectedDateTime = new Date(`${dateInput.value}T${timeInput.value}`);
                const now = new Date();
                
                if (selectedDateTime <= now) {
                    e.preventDefault();
                    this.showAlert('Please select a future date and time', 'warning');
                }
            }
        });
    }
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.clinicApp = new ClinicApp();
});

// Utility functions for charts (if Chart.js is available)
if (typeof Chart !== 'undefined') {
    // Dashboard charts
    function initDashboardCharts() {
        // Appointments chart
        const appointmentsCtx = document.getElementById('appointmentsChart');
        if (appointmentsCtx) {
            new Chart(appointmentsCtx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Appointments',
                        data: [12, 19, 15, 25, 22, 18, 20],
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }]
                }
            });
        }

        // Queue status chart
        const queueCtx = document.getElementById('queueChart');
        if (queueCtx) {
            new Chart(queueCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Waiting', 'In Progress', 'Completed'],
                    datasets: [{
                        data: [5, 2, 25],
                        backgroundColor: ['#ffc107', '#17a2b8', '#28a745']
                    }]
                }
            });
        }
    }

    // Initialize charts when DOM is ready
    document.addEventListener('DOMContentLoaded', initDashboardCharts);
}
