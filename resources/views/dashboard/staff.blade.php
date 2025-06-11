@extends('layouts.app')

@section('title', 'Staff Dashboard - Clinic Management System')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Staff Dashboard</h1>
        <p class="text-muted">Welcome back, {{ auth()->user()->full_name }}</p>
    </div>
    <div class="text-end">
        <small class="text-muted">{{ now()->format('l, F j, Y') }}</small>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h5 class="text-uppercase mb-0" style="font-size: 0.85rem;">Total Patients</h5>
                        <span class="h2 font-weight-bold mb-0">{{ $stats['total_patients'] }}</span>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users stats-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h5 class="text-uppercase mb-0" style="font-size: 0.85rem;">Today's Appointments</h5>
                        <span class="h2 font-weight-bold mb-0">{{ $stats['today_appointments'] }}</span>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check stats-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%);">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h5 class="text-uppercase mb-0" style="font-size: 0.85rem;">Pending Queue</h5>
                        <span class="h2 font-weight-bold mb-0">{{ $stats['pending_queue'] }}</span>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-list-ol stats-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h5 class="text-uppercase mb-0" style="font-size: 0.85rem;">Completed Today</h5>
                        <span class="h2 font-weight-bold mb-0">{{ $stats['completed_today'] }}</span>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle stats-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Today's Appointments -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-day me-2"></i>Today's Appointments
                </h5>
                <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @if($todayAppointments->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($todayAppointments->take(5) as $appointment)
                            <div class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">{{ $appointment->patient->full_name }}</div>
                                    <small class="text-muted">
                                        {{ $appointment->formatted_time }} - 
                                        {{ $appointment->appointment_type }} with 
                                        {{ $appointment->doctor->full_name }}
                                    </small>
                                </div>
                                <span class="badge bg-{{ $appointment->status_color }} rounded-pill">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No appointments scheduled for today</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Current Queue -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-users-line me-2"></i>Current Queue
                </h5>
                <a href="{{ route('queue.management') }}" class="btn btn-sm btn-outline-primary">Manage Queue</a>
            </div>
            <div class="card-body">
                @if($currentQueue->count() > 0)
                    <div class="list-group list-group-flush" id="queueList">
                        @foreach($currentQueue as $ticket)
                            <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                <div>
                                    <div class="queue-number">{{ $ticket->queue_number }}</div>
                                    <div class="fw-bold">{{ $ticket->patient_name }}</div>
                                    <small class="text-muted">{{ $ticket->transaction_type }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="priority-badge priority-{{ $ticket->priority_type }}">
                                        {{ ucfirst($ticket->priority_type) }}
                                    </span>
                                    <div class="mt-1">
                                        <small class="text-muted">Est: {{ $ticket->estimated_wait }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No patients in queue</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Medical Records -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-file-medical me-2"></i>Recent Medical Records
                </h5>
                <a href="{{ route('medical-records.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @if($recentRecords->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Type</th>
                                    <th>Diagnosis</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentRecords as $record)
                                    <tr>
                                        <td>{{ $record->date_time->format('M d, Y') }}</td>
                                        <td>{{ $record->patient->full_name }}</td>
                                        <td>{{ $record->doctor->full_name ?? 'N/A' }}</td>
                                        <td>{{ ucfirst($record->transaction_type) }}</td>
                                        <td>{{ Str::limit($record->initial_diagnosis, 30) }}</td>
                                        <td>
                                            <a href="{{ route('medical-records.show', $record) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No recent medical records</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('patients.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Add New Patient
                    </a>
                    <a href="{{ route('appointments.create') }}" class="btn btn-success">
                        <i class="fas fa-calendar-plus me-2"></i>Schedule Appointment
                    </a>
                    <a href="{{ route('medical-records.create') }}" class="btn btn-info">
                        <i class="fas fa-file-medical-alt me-2"></i>New Medical Record
                    </a>
                    <a href="{{ route('queue.kiosk') }}" class="btn btn-warning" target="_blank">
                        <i class="fas fa-ticket-alt me-2"></i>Queue Kiosk
                    </a>
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('admin.staff.create') }}" class="btn btn-secondary">
                            <i class="fas fa-user-tie me-2"></i>Add Staff Member
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-heartbeat me-2"></i>System Status
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <i class="fas fa-server fa-2x text-success mb-2"></i>
                            <div class="fw-bold">Server</div>
                            <small class="text-success">Online</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <i class="fas fa-database fa-2x text-success mb-2"></i>
                        <div class="fw-bold">Database</div>
                        <small class="text-success">Connected</small>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <small class="text-muted">Last updated: <span id="lastUpdated">{{ now()->format('h:i A') }}</span></small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh queue data every 30 seconds
    setInterval(function() {
        fetch('{{ route("dashboard.data") }}')
            .then(response => response.json())
            .then(data => {
                // Update queue list
                updateQueueList(data.current_queue);
                
                // Update timestamp
                document.getElementById('lastUpdated').textContent = new Date().toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });
            })
            .catch(error => console.error('Error refreshing data:', error));
    }, 30000);

    function updateQueueList(queueData) {
        const queueList = document.getElementById('queueList');
        if (!queueList) return;

        if (queueData.length === 0) {
            queueList.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">No patients in queue</h6>
                </div>
            `;
            return;
        }

        const html = queueData.map(ticket => `
            <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                <div>
                    <div class="queue-number">${ticket.queue_number}</div>
                    <div class="fw-bold">${ticket.patient_name}</div>
                    <small class="text-muted">${ticket.transaction_type}</small>
                </div>
                <div class="text-end">
                    <span class="priority-badge priority-${ticket.priority_type}">
                        ${ticket.priority_type.charAt(0).toUpperCase() + ticket.priority_type.slice(1)}
                    </span>
                    <div class="mt-1">
                        <small class="text-muted">Est: ${ticket.estimated_wait}</small>
                    </div>
                </div>
            </div>
        `).join('');

        queueList.innerHTML = html;
    }
</script>
@endpush
