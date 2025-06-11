<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Status - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .mobile-container {
            max-width: 500px;
            margin: 0 auto;
            min-height: 100vh;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .mobile-header {
            background: linear-gradient(45deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .serving-card {
            background: linear-gradient(45deg, #10b981, #059669);
            color: white;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        .queue-item {
            background: #f8fafc;
            border-left: 4px solid #e5e7eb;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 0 10px 10px 0;
        }
        .queue-item.priority-1 { border-left-color: #fbbf24; }
        .queue-item.priority-2 { border-left-color: #34d399; }
        .queue-item.priority-3 { border-left-color: #f87171; }
        .queue-item.priority-4 { border-left-color: #a5b4fc; }
        
        .user-position {
            background: linear-gradient(45deg, #f59e0b, #d97706);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .refresh-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <div class="mobile-header">
            <div class="d-flex align-items-center justify-content-between">
                <a href="{{ route('mobile.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-1">Queue Status</h4>
                    <small>{{ now()->format('M d, Y - h:i A') }}</small>
                </div>
                <button class="btn btn-outline-light btn-sm" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
        
        <div class="p-4">
            <!-- User's Position (if they provided queue number) -->
            @if($userQueue)
            <div class="user-position">
                <h5 class="mb-2">
                    <i class="fas fa-ticket-alt me-2"></i>
                    Your Queue: {{ $userQueue->queue_number }}
                </h5>
                <div class="row">
                    <div class="col-6">
                        <h3 class="mb-0">{{ $userPosition }}</h3>
                        <small>Position in Queue</small>
                    </div>
                    <div class="col-6">
                        <h3 class="mb-0">{{ $userQueue->priority }}</h3>
                        <small>Priority Level</small>
                    </div>
                </div>
            </div>
            @else
            <!-- Queue Number Input -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title">Track Your Queue</h6>
                    <form method="GET" action="{{ route('mobile.queue-status') }}">
                        <div class="input-group">
                            <input type="text" name="queue_number" class="form-control" 
                                   placeholder="Enter your queue number" 
                                   value="{{ request('queue_number') }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Currently Serving -->
            @if($currentQueue->count() > 0)
            <h6 class="mb-3">
                <i class="fas fa-bullhorn me-2"></i>
                Currently Serving
            </h6>
            @foreach($currentQueue as $serving)
            <div class="serving-card">
                <div class="card-body text-center">
                    <h3 class="mb-2">{{ $serving->queue_number }}</h3>
                    <p class="mb-1">{{ $serving->patient->full_name }}</p>
                    <small>
                        <i class="fas fa-clinic-medical me-1"></i>
                        Window {{ $serving->window_number }}
                    </small>
                </div>
            </div>
            @endforeach
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No patients currently being served.
            </div>
            @endif

            <!-- Waiting Queue -->
            <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                <h6 class="mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Waiting Queue
                </h6>
                <span class="badge bg-secondary">{{ $waitingQueue->count() }} waiting</span>
            </div>

            @if($waitingQueue->count() > 0)
                @foreach($waitingQueue as $index => $queue)
                <div class="queue-item priority-{{ $queue->priority }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $queue->queue_number }}</h6>
                            <small class="text-muted">
                                {{ $queue->patient->patient_type }} - 
                                {{ $queue->created_at->format('h:i A') }}
                            </small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary">{{ $index + 1 }}</span>
                            <br>
                            <small class="text-muted">
                                @if($queue->priority == 1)
                                    Faculty
                                @elseif($queue->priority == 2)
                                    Personnel
                                @elseif($queue->priority == 3)
                                    Senior
                                @else
                                    Regular
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5>No One Waiting</h5>
                    <p class="text-muted">The queue is currently empty.</p>
                </div>
            @endif

            <!-- Statistics -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Today's Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h5 class="text-primary mb-1">{{ $currentQueue->count() }}</h5>
                            <small class="text-muted">Serving</small>
                        </div>
                        <div class="col-4">
                            <h5 class="text-warning mb-1">{{ $waitingQueue->count() }}</h5>
                            <small class="text-muted">Waiting</small>
                        </div>
                        <div class="col-4">
                            <h5 class="text-success mb-1">0</h5>
                            <small class="text-muted">Completed</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="alert alert-info mt-4">
                <h6><i class="fas fa-info-circle me-2"></i>Instructions</h6>
                <ul class="small mb-0">
                    <li>This page refreshes automatically every 30 seconds</li>
                    <li>Listen for your queue number announcement</li>
                    <li>Present your ticket to the nurse when called</li>
                    <li>If you miss your turn, inform the staff immediately</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Floating Refresh Button -->
    <button class="btn btn-primary refresh-btn" onclick="location.reload()" title="Refresh">
        <i class="fas fa-sync-alt"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh every 30 seconds
        setInterval(function() {
            location.reload();
        }, 30000);
        
        // Show loading indicator during refresh
        let refreshBtn = document.querySelector('.refresh-btn');
        refreshBtn.addEventListener('click', function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            setTimeout(() => {
                location.reload();
            }, 500);
        });
        
        // Notification sound when user's number is close (if they provided queue number)
        @if($userQueue && $userPosition <= 3)
        if ('Notification' in window) {
            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    new Notification('Queue Alert', {
                        body: 'Your turn is coming soon! You are number {{ $userPosition }} in queue.',
                        icon: '/favicon.ico'
                    });
                }
            });
        }
        @endif
    </script>
</body>
</html>
