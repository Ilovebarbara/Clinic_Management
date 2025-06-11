<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Number - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            body * { visibility: hidden; }
            .printable, .printable * { visibility: visible; }
            .printable { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .ticket-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
        }
        
        .ticket {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 2px dashed #ddd;
        }
        
        .ticket-header {
            background: linear-gradient(45deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .queue-number {
            font-size: 4rem;
            font-weight: bold;
            color: #3b82f6;
            text-align: center;
            margin: 30px 0;
        }
        
        .priority-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .priority-1 { background: #fbbf24; color: #92400e; }
        .priority-2 { background: #34d399; color: #065f46; }
        .priority-3 { background: #f87171; color: #991b1b; }
        .priority-4 { background: #a5b4fc; color: #3730a3; }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket printable">
            <div class="ticket-header">
                <h4 class="mb-1">
                    <i class="fas fa-hospital-alt me-2"></i>
                    University Clinic
                </h4>
                <small>Queue Ticket</small>
            </div>
            
            <div class="p-4">
                <div class="text-center mb-4">
                    <div class="queue-number">{{ $queueTicket->queue_number }}</div>
                    <div class="mb-3">
                        <span class="priority-badge priority-{{ $queueTicket->priority }}">
                            @if($queueTicket->priority == 1)
                                Faculty Priority
                            @elseif($queueTicket->priority == 2)
                                Personnel Priority
                            @elseif($queueTicket->priority == 3)
                                Senior Citizen Priority
                            @else
                                Regular Queue
                            @endif
                        </span>
                    </div>
                </div>
                
                <div class="patient-info">
                    <h6 class="mb-3">Patient Information:</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td>{{ $queueTicket->patient->full_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Type:</strong></td>
                            <td>{{ ucfirst($queueTicket->patient->patient_type) }}</td>
                        </tr>
                        @if($queueTicket->patient->student_id)
                        <tr>
                            <td><strong>ID:</strong></td>
                            <td>{{ $queueTicket->patient->student_id }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Time:</strong></td>
                            <td>{{ $queueTicket->created_at->format('h:i A') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Date:</strong></td>
                            <td>{{ $queueTicket->created_at->format('M d, Y') }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="queue-info bg-light p-3 rounded mb-3">
                    <div class="row text-center">
                        <div class="col">
                            <h6 class="mb-1">Your Position</h6>
                            <span class="fs-4 fw-bold text-primary">{{ $position }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="instructions">
                    <h6>Instructions:</h6>
                    <ul class="small">
                        <li>Please keep this ticket with you</li>
                        <li>Wait for your number to be called</li>
                        <li>Check the display screen for updates</li>
                        <li>Present this ticket to the nurse</li>
                    </ul>
                </div>
                
                <div class="text-center mt-4 no-print">
                    <button onclick="window.print()" class="btn btn-primary me-2">
                        <i class="fas fa-print me-2"></i>Print Ticket
                    </button>
                    <a href="{{ route('kiosk.index') }}" class="btn btn-secondary">
                        <i class="fas fa-home me-2"></i>Back to Home
                    </a>
                </div>
                
                <div class="text-center mt-3 no-print">
                    <a href="{{ route('mobile.queue-status') }}?queue_number={{ $queueTicket->queue_number }}" 
                       class="btn btn-outline-info btn-sm">
                        <i class="fas fa-mobile-alt me-2"></i>Track on Mobile
                    </a>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4 no-print">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Clinic Hours:</strong> Mon-Fri 8AM-5PM, Sat 8AM-12PM
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto print on page load (optional)
        // window.onload = function() { window.print(); }
        
        // Auto-refresh queue position every 30 seconds
        setInterval(function() {
            if (!window.location.search.includes('no_refresh')) {
                location.reload();
            }
        }, 30000);
    </script>
</body>
</html>
