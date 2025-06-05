// Queue Management System JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeQueueSystem();
});

function initializeQueueSystem() {
    initializeKiosk();
    initializeQueueDisplay();
    initializeQueueManagement();
    startAutoRefresh();
}

// Kiosk Interface Functions
function initializeKiosk() {
    const kioskContainer = document.querySelector('.kiosk-container');
    if (!kioskContainer) return;

    // Service selection buttons
    document.querySelectorAll('.service-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            selectService(this.dataset.service);
        });
    });

    // Priority selection
    document.querySelectorAll('input[name="priority"]').forEach(radio => {
        radio.addEventListener('change', updatePriorityInfo);
    });

    // Generate ticket button
    const generateBtn = document.getElementById('generate-ticket');
    if (generateBtn) {
        generateBtn.addEventListener('click', generateTicket);
    }
}

function selectService(serviceType) {
    // Remove active class from all buttons
    document.querySelectorAll('.service-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Add active class to selected button
    event.target.classList.add('active');
    
    // Show priority selection
    document.getElementById('priority-selection').style.display = 'block';
    
    // Enable generate button
    document.getElementById('generate-ticket').disabled = false;
}

function updatePriorityInfo() {
    const selectedPriority = document.querySelector('input[name="priority"]:checked');
    if (!selectedPriority) return;

    const infoElement = document.getElementById('priority-info');
    const waitTime = document.getElementById('estimated-wait');
    
    let waitMinutes;
    let infoText;
    
    switch(selectedPriority.value) {
        case 'emergency':
            waitMinutes = 0;
            infoText = 'Emergency cases are seen immediately';
            break;
        case 'urgent':
            waitMinutes = 15;
            infoText = 'Urgent cases typically wait 10-20 minutes';
            break;
        case 'normal':
            waitMinutes = 45;
            infoText = 'Normal cases may wait 30-60 minutes';
            break;
    }
    
    infoElement.textContent = infoText;
    waitTime.textContent = `${waitMinutes} minutes`;
}

function generateTicket() {
    const selectedService = document.querySelector('.service-btn.active');
    const selectedPriority = document.querySelector('input[name="priority"]:checked');
    
    if (!selectedService || !selectedPriority) {
        alert('Please select a service and priority level');
        return;
    }

    const ticketData = {
        service: selectedService.dataset.service,
        priority: selectedPriority.value,
        timestamp: new Date().toISOString()
    };

    fetch('/queue/generate-ticket', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(ticketData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showTicket(data.ticket);
        } else {
            alert('Error generating ticket: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error generating ticket');
    });
}

function showTicket(ticketData) {
    // Hide kiosk interface
    document.querySelector('.kiosk-interface').style.display = 'none';
    
    // Show ticket
    const ticketContainer = document.querySelector('.ticket-container');
    ticketContainer.style.display = 'block';
    
    // Fill ticket data
    document.getElementById('ticket-number').textContent = ticketData.number;
    document.getElementById('ticket-service').textContent = ticketData.service;
    document.getElementById('ticket-priority').textContent = ticketData.priority;
    document.getElementById('ticket-time').textContent = new Date(ticketData.timestamp).toLocaleTimeString();
    
    // Generate QR code
    generateQRCode(ticketData.number);
    
    // Auto-print if enabled
    if (ticketData.auto_print) {
        setTimeout(() => window.print(), 1000);
    }
    
    // Show new ticket button after 5 seconds
    setTimeout(() => {
        document.getElementById('new-ticket-btn').style.display = 'block';
    }, 5000);
}

function generateQRCode(ticketNumber) {
    const qrContainer = document.getElementById('ticket-qr');
    if (qrContainer && typeof QRCode !== 'undefined') {
        qrContainer.innerHTML = '';
        new QRCode(qrContainer, {
            text: `TICKET-${ticketNumber}`,
            width: 100,
            height: 100
        });
    }
}

// Queue Display Functions
function initializeQueueDisplay() {
    const queueDisplay = document.querySelector('.queue-display');
    if (!queueDisplay) return;

    updateQueueDisplay();
}

function updateQueueDisplay() {
    fetch('/queue/current-status')
        .then(response => response.json())
        .then(data => {
            updateCurrentTicket(data.current);
            updateWaitingQueue(data.waiting);
            updateQueueStats(data.stats);
        })
        .catch(error => console.error('Error updating queue display:', error));
}

function updateCurrentTicket(currentTicket) {
    const currentDisplay = document.getElementById('current-ticket');
    if (currentDisplay) {
        if (currentTicket) {
            currentDisplay.innerHTML = `
                <div class="current-ticket-number">${currentTicket.number}</div>
                <div class="current-ticket-service">${currentTicket.service}</div>
                <div class="current-ticket-counter">Counter ${currentTicket.counter || 1}</div>
            `;
            currentDisplay.className = `current-ticket priority-${currentTicket.priority}`;
        } else {
            currentDisplay.innerHTML = '<div class="no-ticket">No patient being served</div>';
            currentDisplay.className = 'current-ticket';
        }
    }
}

function updateWaitingQueue(waitingTickets) {
    const waitingList = document.getElementById('waiting-queue');
    if (!waitingList) return;

    waitingList.innerHTML = '';
    
    if (waitingTickets.length === 0) {
        waitingList.innerHTML = '<div class="no-waiting">No patients waiting</div>';
        return;
    }

    waitingTickets.forEach(ticket => {
        const ticketEl = document.createElement('div');
        ticketEl.className = `waiting-ticket priority-${ticket.priority}`;
        ticketEl.innerHTML = `
            <span class="ticket-number">${ticket.number}</span>
            <span class="ticket-service">${ticket.service}</span>
            <span class="ticket-wait-time">${ticket.wait_time || 'N/A'}</span>
        `;
        waitingList.appendChild(ticketEl);
    });
}

function updateQueueStats(stats) {
    if (stats) {
        updateStat('total-waiting', stats.total_waiting);
        updateStat('average-wait', stats.average_wait);
        updateStat('served-today', stats.served_today);
    }
}

function updateStat(elementId, value) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = value;
    }
}

// Queue Management Functions (for staff)
function initializeQueueManagement() {
    const managementPanel = document.querySelector('.queue-management');
    if (!managementPanel) return;

    // Call next button
    const callNextBtn = document.getElementById('call-next');
    if (callNextBtn) {
        callNextBtn.addEventListener('click', callNextPatient);
    }

    // Complete current button
    const completeBtn = document.getElementById('complete-current');
    if (completeBtn) {
        completeBtn.addEventListener('click', completeCurrentPatient);
    }

    // Skip patient button
    const skipBtn = document.getElementById('skip-patient');
    if (skipBtn) {
        skipBtn.addEventListener('click', skipCurrentPatient);
    }
}

function callNextPatient() {
    fetch('/queue/call-next', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateQueueDisplay();
            showNotification('Next patient called', 'success');
        } else {
            showNotification(data.message || 'No patients in queue', 'info');
        }
    })
    .catch(error => {
        console.error('Error calling next patient:', error);
        showNotification('Error calling next patient', 'error');
    });
}

function completeCurrentPatient() {
    fetch('/queue/complete-current', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateQueueDisplay();
            showNotification('Patient consultation completed', 'success');
        } else {
            showNotification(data.message || 'No current patient', 'info');
        }
    })
    .catch(error => {
        console.error('Error completing patient:', error);
        showNotification('Error completing consultation', 'error');
    });
}

function skipCurrentPatient() {
    if (confirm('Are you sure you want to skip this patient?')) {
        fetch('/queue/skip-current', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateQueueDisplay();
                showNotification('Patient skipped', 'warning');
            } else {
                showNotification(data.message || 'No current patient', 'info');
            }
        })
        .catch(error => {
            console.error('Error skipping patient:', error);
            showNotification('Error skipping patient', 'error');
        });
    }
}

// Auto-refresh functionality
function startAutoRefresh() {
    // Refresh queue display every 10 seconds
    setInterval(updateQueueDisplay, 10000);
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} notification-toast`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    .stat-updated {
        animation: pulse 0.5s ease;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
`;
document.head.appendChild(style);

// Export functions for global use
window.queueSystem = {
    updateQueueDisplay,
    callNextPatient,
    completeCurrentPatient,
    skipCurrentPatient,
    showNotification
};
