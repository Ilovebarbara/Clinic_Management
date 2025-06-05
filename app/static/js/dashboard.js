// Dashboard Analytics and Charts
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard features
    initializeCharts();
    initializeRealTimeUpdates();
    initializeNotifications();
});

// Initialize Chart.js charts
function initializeCharts() {
    // Patients by Age Group Chart
    const ageGroupChart = document.getElementById('ageGroupChart');
    if (ageGroupChart) {
        new Chart(ageGroupChart, {
            type: 'doughnut',
            data: {
                labels: ['0-18', '19-30', '31-50', '51-70', '70+'],
                datasets: [{
                    data: [25, 35, 20, 15, 5],
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Appointments This Week Chart
    const appointmentsChart = document.getElementById('appointmentsChart');
    if (appointmentsChart) {
        new Chart(appointmentsChart, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Appointments',
                    data: [12, 19, 8, 15, 22, 18, 7],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Queue Status Chart
    const queueChart = document.getElementById('queueChart');
    if (queueChart) {
        new Chart(queueChart, {
            type: 'bar',
            data: {
                labels: ['General', 'Emergency', 'Follow-up', 'Consultation'],
                datasets: [{
                    label: 'In Queue',
                    data: [8, 3, 5, 12],
                    backgroundColor: ['#28a745', '#dc3545', '#ffc107', '#007bff']
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

// Real-time updates for dashboard data
function initializeRealTimeUpdates() {
    // Update every 30 seconds
    setInterval(updateDashboardData, 30000);
}

function updateDashboardData() {
    fetch('/api/dashboard-stats')
        .then(response => response.json())
        .then(data => {
            // Update statistics cards
            updateStatCard('total-patients', data.total_patients);
            updateStatCard('todays-appointments', data.todays_appointments);
            updateStatCard('queue-length', data.queue_length);
            updateStatCard('active-staff', data.active_staff);
        })
        .catch(error => console.error('Error updating dashboard:', error));
}

function updateStatCard(elementId, value) {
    const element = document.getElementById(elementId);
    if (element) {
        const current = parseInt(element.textContent);
        if (current !== value) {
            element.textContent = value;
            element.parentElement.classList.add('stat-updated');
            setTimeout(() => {
                element.parentElement.classList.remove('stat-updated');
            }, 1000);
        }
    }
}

// Notification system
function initializeNotifications() {
    // Check for new notifications every minute
    setInterval(checkNotifications, 60000);
}

function checkNotifications() {
    fetch('/api/notifications')
        .then(response => response.json())
        .then(data => {
            if (data.notifications && data.notifications.length > 0) {
                showNotifications(data.notifications);
            }
        })
        .catch(error => console.error('Error checking notifications:', error));
}

function showNotifications(notifications) {
    const container = document.getElementById('notification-container') || createNotificationContainer();
    
    notifications.forEach(notification => {
        const notificationEl = document.createElement('div');
        notificationEl.className = `alert alert-${notification.type} alert-dismissible fade show`;
        notificationEl.innerHTML = `
            <i class="fas fa-${getNotificationIcon(notification.type)}"></i>
            ${notification.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        container.appendChild(notificationEl);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notificationEl.parentElement) {
                notificationEl.remove();
            }
        }, 5000);
    });
}

function createNotificationContainer() {
    const container = document.createElement('div');
    container.id = 'notification-container';
    container.style.position = 'fixed';
    container.style.top = '20px';
    container.style.right = '20px';
    container.style.zIndex = '9999';
    container.style.maxWidth = '400px';
    document.body.appendChild(container);
    return container;
}

function getNotificationIcon(type) {
    const icons = {
        'success': 'check-circle',
        'info': 'info-circle',
        'warning': 'exclamation-triangle',
        'danger': 'exclamation-circle'
    };
    return icons[type] || 'bell';
}

// Export functions for use in other scripts
window.dashboardUtils = {
    updateDashboardData,
    showNotifications,
    initializeCharts
};
