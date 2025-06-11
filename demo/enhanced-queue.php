<?php
/**
 * Enhanced Queue Management System
 * Real-time queue updates with WebSocket-like functionality using Server-Sent Events
 */

require_once '../minimal-system.php';

class EnhancedQueueManager {
    private $db;
    
    public function __construct() {
        global $pdo;
        $this->db = $pdo;
        
        // Create queue_events table if it doesn't exist
        $this->createQueueEventsTable();
    }
    
    private function createQueueEventsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS queue_events (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            event_type VARCHAR(50) NOT NULL,
            ticket_id INTEGER NOT NULL,
            ticket_number VARCHAR(20) NOT NULL,
            window_number INTEGER,
            priority INTEGER DEFAULT 4,
            status VARCHAR(50) DEFAULT 'waiting',
            patient_name VARCHAR(255),
            service_type VARCHAR(255),
            event_data TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            processed BOOLEAN DEFAULT FALSE
        )";
        
        $this->db->exec($sql);
    }
    
    public function addQueueEvent($eventType, $ticketData) {
        $sql = "INSERT INTO queue_events (
            event_type, ticket_id, ticket_number, window_number, 
            priority, status, patient_name, service_type, event_data
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $eventType,
            $ticketData['ticket_id'] ?? 0,
            $ticketData['ticket_number'],
            $ticketData['window_number'] ?? null,
            $ticketData['priority'] ?? 4,
            $ticketData['status'] ?? 'waiting',
            $ticketData['patient_name'] ?? '',
            $ticketData['service_type'] ?? '',
            json_encode($ticketData)
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function getUnprocessedEvents($since = null) {
        $sql = "SELECT * FROM queue_events WHERE processed = FALSE";
        $params = [];
        
        if ($since) {
            $sql .= " AND created_at > ?";
            $params[] = $since;
        }
        
        $sql .= " ORDER BY created_at ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function markEventsProcessed($eventIds) {
        if (empty($eventIds)) return;
        
        $placeholders = str_repeat('?,', count($eventIds) - 1) . '?';
        $sql = "UPDATE queue_events SET processed = TRUE WHERE id IN ($placeholders)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($eventIds);
    }
    
    public function callNextTicket($windowNumber) {
        // Get next ticket based on priority
        $sql = "SELECT * FROM queue_tickets 
                WHERE status = 'waiting' 
                ORDER BY priority ASC, id ASC 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($ticket) {
            // Update ticket status
            $updateSql = "UPDATE queue_tickets 
                         SET status = 'called', window_number = ?, called_at = CURRENT_TIMESTAMP 
                         WHERE id = ?";
            $updateStmt = $this->db->prepare($updateSql);
            $updateStmt->execute([$windowNumber, $ticket['id']]);
            
            // Add event
            $this->addQueueEvent('ticket_called', [
                'ticket_id' => $ticket['id'],
                'ticket_number' => $ticket['ticket_number'],
                'window_number' => $windowNumber,
                'priority' => $ticket['priority'],
                'status' => 'called',
                'patient_name' => $ticket['patient_name'],
                'service_type' => $ticket['service_type']
            ]);
            
            return $ticket;
        }
        
        return null;
    }
    
    public function completeTicket($ticketId) {
        $sql = "UPDATE queue_tickets 
                SET status = 'completed', completed_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ticketId]);
        
        // Get ticket details for event
        $ticketSql = "SELECT * FROM queue_tickets WHERE id = ?";
        $ticketStmt = $this->db->prepare($ticketSql);
        $ticketStmt->execute([$ticketId]);
        $ticket = $ticketStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($ticket) {
            $this->addQueueEvent('ticket_completed', [
                'ticket_id' => $ticket['id'],
                'ticket_number' => $ticket['ticket_number'],
                'window_number' => $ticket['window_number'],
                'priority' => $ticket['priority'],
                'status' => 'completed',
                'patient_name' => $ticket['patient_name'],
                'service_type' => $ticket['service_type']
            ]);
        }
        
        return $ticket;
    }
    
    public function addNewTicket($patientData) {
        // Generate ticket number
        $ticketNumber = $this->generateTicketNumber();
        
        // Insert ticket
        $sql = "INSERT INTO queue_tickets (
            ticket_number, patient_name, patient_id, service_type, 
            priority, status, phone, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $ticketNumber,
            $patientData['patient_name'],
            $patientData['patient_id'],
            $patientData['service_type'],
            $patientData['priority'] ?? 4,
            'waiting',
            $patientData['phone'] ?? ''
        ]);
        
        $ticketId = $this->db->lastInsertId();
        
        // Add event
        $this->addQueueEvent('ticket_created', [
            'ticket_id' => $ticketId,
            'ticket_number' => $ticketNumber,
            'priority' => $patientData['priority'] ?? 4,
            'status' => 'waiting',
            'patient_name' => $patientData['patient_name'],
            'service_type' => $patientData['service_type']
        ]);
        
        return [
            'id' => $ticketId,
            'ticket_number' => $ticketNumber,
            'estimated_wait' => $this->calculateEstimatedWait($patientData['priority'] ?? 4)
        ];
    }
    
    private function generateTicketNumber() {
        // Get today's ticket count
        $sql = "SELECT COUNT(*) as count FROM queue_tickets 
                WHERE DATE(created_at) = DATE('now')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $count = $result['count'] + 1;
        return 'Q' . date('md') . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
    
    private function calculateEstimatedWait($priority) {
        // Count tickets ahead with higher or equal priority
        $sql = "SELECT COUNT(*) as count FROM queue_tickets 
                WHERE status = 'waiting' AND priority <= ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$priority]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $ticketsAhead = $result['count'];
        $avgServiceTime = 10; // 10 minutes average
        
        return $ticketsAhead * $avgServiceTime;
    }
    
    public function getQueueStatus() {
        // Get current queue
        $sql = "SELECT * FROM queue_tickets 
                WHERE status IN ('waiting', 'called') 
                ORDER BY priority ASC, id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $queue = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get window status
        $windows = [
            ['id' => 1, 'name' => 'Window 1', 'status' => 'Available', 'current_ticket' => null],
            ['id' => 2, 'name' => 'Window 2', 'status' => 'Available', 'current_ticket' => null],
            ['id' => 3, 'name' => 'Window 3', 'status' => 'Available', 'current_ticket' => null],
            ['id' => 4, 'name' => 'Emergency', 'status' => 'Available', 'current_ticket' => null]
        ];
        
        // Update window status based on called tickets
        foreach ($queue as $ticket) {
            if ($ticket['status'] === 'called' && $ticket['window_number']) {
                $windowIndex = $ticket['window_number'] - 1;
                if (isset($windows[$windowIndex])) {
                    $windows[$windowIndex]['status'] = 'Busy';
                    $windows[$windowIndex]['current_ticket'] = $ticket['ticket_number'];
                }
            }
        }
        
        return [
            'queue' => $queue,
            'windows' => $windows,
            'total_waiting' => count(array_filter($queue, fn($t) => $t['status'] === 'waiting')),
            'total_called' => count(array_filter($queue, fn($t) => $t['status'] === 'called')),
            'last_updated' => date('Y-m-d H:i:s')
        ];
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $queueManager = new EnhancedQueueManager();
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'get_events':
            $since = $_POST['since'] ?? null;
            $events = $queueManager->getUnprocessedEvents($since);
            echo json_encode(['success' => true, 'events' => $events]);
            break;
            
        case 'call_next':
            $windowNumber = intval($_POST['window_number']);
            $ticket = $queueManager->callNextTicket($windowNumber);
            echo json_encode(['success' => true, 'ticket' => $ticket]);
            break;
            
        case 'complete_ticket':
            $ticketId = intval($_POST['ticket_id']);
            $ticket = $queueManager->completeTicket($ticketId);
            echo json_encode(['success' => true, 'ticket' => $ticket]);
            break;
            
        case 'add_ticket':
            $patientData = [
                'patient_name' => $_POST['patient_name'] ?? '',
                'patient_id' => $_POST['patient_id'] ?? '',
                'service_type' => $_POST['service_type'] ?? '',
                'priority' => intval($_POST['priority'] ?? 4),
                'phone' => $_POST['phone'] ?? ''
            ];
            $ticket = $queueManager->addNewTicket($patientData);
            echo json_encode(['success' => true, 'ticket' => $ticket]);
            break;
            
        case 'get_status':
            $status = $queueManager->getQueueStatus();
            echo json_encode(['success' => true, 'status' => $status]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}

// Handle Server-Sent Events for real-time updates
if (isset($_GET['events'])) {
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    
    $queueManager = new EnhancedQueueManager();
    $lastEventId = $_GET['lastEventId'] ?? 0;
    
    while (true) {
        $events = $queueManager->getUnprocessedEvents();
        $newEvents = array_filter($events, fn($e) => $e['id'] > $lastEventId);
        
        if (!empty($newEvents)) {
            foreach ($newEvents as $event) {
                echo "id: {$event['id']}\n";
                echo "event: queue_update\n";
                echo "data: " . json_encode($event) . "\n\n";
                $lastEventId = $event['id'];
            }
            
            // Mark events as processed
            $eventIds = array_column($newEvents, 'id');
            $queueManager->markEventsProcessed($eventIds);
        }
        
        // Send heartbeat
        echo "event: heartbeat\n";
        echo "data: " . json_encode(['timestamp' => time()]) . "\n\n";
        
        if (ob_get_level()) {
            ob_flush();
        }
        flush();
        
        sleep(2); // Check every 2 seconds
        
        // Break if connection is closed
        if (connection_aborted()) {
            break;
        }
    }
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Queue Management - University Clinic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../resources/js/queue-notifications.js"></script>
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
        
        .notification-enter {
            animation: slideInRight 0.3s ease-out;
        }
        
        .notification-exit {
            animation: slideOutRight 0.3s ease-out;
        }
        
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        
        .pulse-ring {
            animation: pulse-ring 2s infinite;
        }
        
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.5; }
            100% { transform: scale(0.8); opacity: 1; }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="glass-morphism rounded-2xl p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        <i class="fas fa-users mr-3"></i>Enhanced Queue Management
                    </h1>
                    <p class="text-white/80">Real-time queue monitoring with notifications</p>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Audio Toggle -->
                    <button id="toggleAudio" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-all duration-300" title="Enable Audio Notifications">
                        <i class="fas fa-volume-up"></i>
                    </button>
                    
                    <!-- Connection Status -->
                    <div id="connectionStatus" class="flex items-center">
                        <div class="w-3 h-3 bg-green-400 rounded-full pulse-ring mr-2"></div>
                        <span class="text-white text-sm">Connected</span>
                    </div>
                    
                    <!-- Last Update -->
                    <div class="text-white/80 text-sm">
                        Last update: <span id="lastUpdate">--:--:--</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Control Panel -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="glass-morphism rounded-xl p-4">
                <h3 class="text-white font-semibold mb-2">
                    <i class="fas fa-plus mr-2"></i>Add Ticket
                </h3>
                <button onclick="showAddTicketModal()" class="w-full bg-white/20 hover:bg-white/30 text-white py-2 rounded-lg transition-all duration-300">
                    New Ticket
                </button>
            </div>
            
            <div class="glass-morphism rounded-xl p-4">
                <h3 class="text-white font-semibold mb-2">
                    <i class="fas fa-play mr-2"></i>Call Next
                </h3>
                <select id="windowSelect" class="w-full bg-white/20 text-white rounded-lg p-2 mb-2">
                    <option value="1">Window 1</option>
                    <option value="2">Window 2</option>
                    <option value="3">Window 3</option>
                    <option value="4">Emergency</option>
                </select>
                <button onclick="callNextTicket()" class="w-full bg-green-500/20 hover:bg-green-500/30 text-white py-2 rounded-lg transition-all duration-300">
                    Call Next
                </button>
            </div>
            
            <div class="glass-morphism rounded-xl p-4">
                <h3 class="text-white font-semibold mb-2">
                    <i class="fas fa-chart-bar mr-2"></i>Statistics
                </h3>
                <div class="text-white text-sm">
                    <div>Waiting: <span id="totalWaiting">0</span></div>
                    <div>Called: <span id="totalCalled">0</span></div>
                </div>
            </div>
            
            <div class="glass-morphism rounded-xl p-4">
                <h3 class="text-white font-semibold mb-2">
                    <i class="fas fa-refresh mr-2"></i>Actions
                </h3>
                <button onclick="refreshQueue()" class="w-full bg-blue-500/20 hover:bg-blue-500/30 text-white py-2 rounded-lg transition-all duration-300">
                    Refresh
                </button>
            </div>
        </div>

        <!-- Queue Display -->
        <div id="queueContainer" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Current Queue -->
            <div class="glass-morphism rounded-2xl p-6">
                <h2 class="text-2xl font-bold text-white mb-4">
                    <i class="fas fa-list mr-3"></i>Current Queue
                </h2>
                <div id="queueList" class="space-y-3">
                    <!-- Queue items will be loaded here -->
                </div>
            </div>
            
            <!-- Window Status -->
            <div class="glass-morphism rounded-2xl p-6">
                <h2 class="text-2xl font-bold text-white mb-4">
                    <i class="fas fa-desktop mr-3"></i>Window Status
                </h2>
                <div id="windowStatus" class="space-y-3">
                    <!-- Window status will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add Ticket Modal -->
    <div id="addTicketModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="glass-morphism rounded-2xl p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-white mb-4">Add New Ticket</h3>
            <form id="addTicketForm" class="space-y-4">
                <input type="text" id="patientName" placeholder="Patient Name" class="w-full bg-white/20 text-white placeholder-white/70 rounded-lg p-3">
                <input type="text" id="patientId" placeholder="Student/Employee ID" class="w-full bg-white/20 text-white placeholder-white/70 rounded-lg p-3">
                <select id="serviceType" class="w-full bg-white/20 text-white rounded-lg p-3">
                    <option value="General Consultation">General Consultation</option>
                    <option value="Medical Certificate">Medical Certificate</option>
                    <option value="Prescription Refill">Prescription Refill</option>
                    <option value="Health Screening">Health Screening</option>
                    <option value="Emergency">Emergency</option>
                </select>
                <select id="priority" class="w-full bg-white/20 text-white rounded-lg p-3">
                    <option value="0">Emergency</option>
                    <option value="1">Faculty</option>
                    <option value="2">Personnel</option>
                    <option value="3">Senior Citizen</option>
                    <option value="4" selected>Regular</option>
                </select>
                <input type="tel" id="patientPhone" placeholder="Phone Number" class="w-full bg-white/20 text-white placeholder-white/70 rounded-lg p-3">
                
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 bg-green-500/20 hover:bg-green-500/30 text-white py-3 rounded-lg transition-all duration-300">
                        Add Ticket
                    </button>
                    <button type="button" onclick="hideAddTicketModal()" class="flex-1 bg-red-500/20 hover:bg-red-500/30 text-white py-3 rounded-lg transition-all duration-300">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Enhanced queue management with real-time updates
        let eventSource = null;
        let queueData = { queue: [], windows: [], total_waiting: 0, total_called: 0 };
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initializeRealTimeUpdates();
            refreshQueue();
            
            // Set audio button initial state
            const audioEnabled = localStorage.getItem('audioEnabled') === 'true';
            const audioButton = document.getElementById('toggleAudio');
            const icon = audioButton.querySelector('i');
            if (audioEnabled) {
                icon.className = 'fas fa-volume-up';
                audioButton.title = 'Disable Audio Notifications';
            } else {
                icon.className = 'fas fa-volume-mute';
                audioButton.title = 'Enable Audio Notifications';
            }
        });
        
        function initializeRealTimeUpdates() {
            if (typeof(EventSource) !== "undefined") {
                eventSource = new EventSource('?events=1');
                
                eventSource.onmessage = function(event) {
                    const data = JSON.parse(event.data);
                    handleRealTimeUpdate(data);
                };
                
                eventSource.addEventListener('queue_update', function(event) {
                    const data = JSON.parse(event.data);
                    handleQueueUpdate(data);
                });
                
                eventSource.addEventListener('heartbeat', function(event) {
                    updateConnectionStatus(true);
                });
                
                eventSource.onerror = function(event) {
                    updateConnectionStatus(false);
                    // Reconnect after 5 seconds
                    setTimeout(() => {
                        if (eventSource.readyState === EventSource.CLOSED) {
                            initializeRealTimeUpdates();
                        }
                    }, 5000);
                };
            }
        }
        
        function handleRealTimeUpdate(data) {
            refreshQueue();
            updateLastUpdateTime();
        }
        
        function handleQueueUpdate(data) {
            console.log('Queue update received:', data);
            
            // Trigger notification system
            if (window.queueNotifications) {
                window.queueNotifications.handleQueueUpdate({
                    type: data.event_type.replace('ticket_', ''),
                    ticket: data.ticket_number,
                    window: data.window_number,
                    priority: data.priority
                });
            }
            
            refreshQueue();
        }
        
        function updateConnectionStatus(connected) {
            const status = document.getElementById('connectionStatus');
            const dot = status.querySelector('div');
            const text = status.querySelector('span');
            
            if (connected) {
                dot.className = 'w-3 h-3 bg-green-400 rounded-full pulse-ring mr-2';
                text.textContent = 'Connected';
            } else {
                dot.className = 'w-3 h-3 bg-red-400 rounded-full mr-2';
                text.textContent = 'Disconnected';
            }
        }
        
        function updateLastUpdateTime() {
            document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString();
        }
        
        async function refreshQueue() {
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=get_status'
                });
                
                const result = await response.json();
                if (result.success) {
                    queueData = result.status;
                    updateQueueDisplay();
                    updateStatistics();
                }
            } catch (error) {
                console.error('Failed to refresh queue:', error);
            }
        }
        
        function updateQueueDisplay() {
            const queueList = document.getElementById('queueList');
            const windowStatus = document.getElementById('windowStatus');
            
            // Update queue list
            queueList.innerHTML = queueData.queue.map(ticket => {
                const priorityColors = {
                    0: 'bg-red-100 text-red-800',
                    1: 'bg-purple-100 text-purple-800',
                    2: 'bg-blue-100 text-blue-800',
                    3: 'bg-green-100 text-green-800',
                    4: 'bg-gray-100 text-gray-800'
                };
                
                const priorityLabels = {
                    0: 'Emergency',
                    1: 'Faculty',
                    2: 'Personnel',
                    3: 'Senior Citizen',
                    4: 'Regular'
                };
                
                const statusColors = {
                    'waiting': 'bg-yellow-100 text-yellow-800',
                    'called': 'bg-blue-100 text-blue-800',
                    'completed': 'bg-green-100 text-green-800'
                };
                
                return `
                    <div class="bg-white/10 rounded-lg p-4 border border-white/20">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-semibold text-white">${ticket.ticket_number}</h4>
                            <div class="flex space-x-2">
                                <span class="px-2 py-1 rounded text-xs ${priorityColors[ticket.priority]}">${priorityLabels[ticket.priority]}</span>
                                <span class="px-2 py-1 rounded text-xs ${statusColors[ticket.status]}">${ticket.status.toUpperCase()}</span>
                            </div>
                        </div>
                        <p class="text-white/80 text-sm mb-1">${ticket.patient_name}</p>
                        <p class="text-white/60 text-xs">${ticket.service_type}</p>
                        ${ticket.window_number ? `<p class="text-white/60 text-xs mt-1">Window: ${ticket.window_number}</p>` : ''}
                        ${ticket.status === 'called' ? `
                            <button onclick="completeTicket(${ticket.id})" class="mt-2 bg-green-500/20 hover:bg-green-500/30 text-white px-3 py-1 rounded text-xs transition-all duration-300">
                                Complete
                            </button>
                        ` : ''}
                    </div>
                `;
            }).join('');
            
            // Update window status
            windowStatus.innerHTML = queueData.windows.map(window => `
                <div class="bg-white/10 rounded-lg p-4 border border-white/20">
                    <div class="flex justify-between items-center">
                        <h4 class="font-semibold text-white">${window.name}</h4>
                        <span class="px-2 py-1 rounded text-xs ${window.status === 'Available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${window.status}
                        </span>
                    </div>
                    ${window.current_ticket ? `<p class="text-white/80 text-sm mt-1">Current: ${window.current_ticket}</p>` : ''}
                </div>
            `).join('');
        }
        
        function updateStatistics() {
            document.getElementById('totalWaiting').textContent = queueData.total_waiting;
            document.getElementById('totalCalled').textContent = queueData.total_called;
        }
        
        async function callNextTicket() {
            const windowNumber = document.getElementById('windowSelect').value;
            
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=call_next&window_number=${windowNumber}`
                });
                
                const result = await response.json();
                if (result.success && result.ticket) {
                    showNotification(`Called ${result.ticket.ticket_number} to Window ${windowNumber}`, 'success');
                } else {
                    showNotification('No tickets waiting', 'info');
                }
            } catch (error) {
                showNotification('Failed to call next ticket', 'error');
            }
        }
        
        async function completeTicket(ticketId) {
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=complete_ticket&ticket_id=${ticketId}`
                });
                
                const result = await response.json();
                if (result.success) {
                    showNotification(`Ticket ${result.ticket.ticket_number} completed`, 'success');
                }
            } catch (error) {
                showNotification('Failed to complete ticket', 'error');
            }
        }
        
        function showAddTicketModal() {
            document.getElementById('addTicketModal').classList.remove('hidden');
            document.getElementById('addTicketModal').classList.add('flex');
        }
        
        function hideAddTicketModal() {
            document.getElementById('addTicketModal').classList.add('hidden');
            document.getElementById('addTicketModal').classList.remove('flex');
        }
        
        document.getElementById('addTicketForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'add_ticket');
            formData.append('patient_name', document.getElementById('patientName').value);
            formData.append('patient_id', document.getElementById('patientId').value);
            formData.append('service_type', document.getElementById('serviceType').value);
            formData.append('priority', document.getElementById('priority').value);
            formData.append('phone', document.getElementById('patientPhone').value);
            
            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                if (result.success) {
                    showNotification(`Ticket ${result.ticket.ticket_number} created`, 'success');
                    hideAddTicketModal();
                    this.reset();
                }
            } catch (error) {
                showNotification('Failed to create ticket', 'error');
            }
        });
        
        function showNotification(message, type = 'info') {
            if (window.queueNotifications) {
                window.queueNotifications.showInAppNotification(message, type);
            }
        }
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (eventSource) {
                eventSource.close();
            }
        });
    </script>
</body>
</html>
