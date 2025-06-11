@extends('layouts.app')

@section('title', 'Appointments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Appointments Management</h4>
                    <a href="{{ route('appointments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Appointment
                    </a>
                </div>
                
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('appointments.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search by patient name or ID..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_from" class="form-control" 
                                       value="{{ request('date_from') }}" placeholder="From Date">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_to" class="form-control" 
                                       value="{{ request('date_to') }}" placeholder="To Date">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" 
                                                {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="doctor_id" class="form-control">
                                    <option value="">All Doctors</option>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" 
                                                {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                            {{ $doctor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Appointments Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Date & Time</th>
                                    <th>Purpose</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments as $appointment)
                                    <tr>
                                        <td>{{ $appointment->id }}</td>
                                        <td>
                                            <strong>{{ $appointment->patient->full_name }}</strong><br>
                                            <small class="text-muted">{{ $appointment->patient->patient_type }}</small>
                                        </td>
                                        <td>{{ $appointment->doctor->name }}</td>
                                        <td>
                                            {{ $appointment->appointment_date->format('M d, Y') }}<br>
                                            <small>{{ $appointment->appointment_time->format('h:i A') }}</small>
                                        </td>
                                        <td>{{ Str::limit($appointment->purpose, 50) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $appointment->status_color }}">
                                                {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('appointments.show', $appointment) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($appointment->status !== 'completed')
                                                    <button type="button" class="btn btn-sm btn-warning" 
                                                            data-toggle="modal" 
                                                            data-target="#updateModal{{ $appointment->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            data-toggle="modal" 
                                                            data-target="#deleteModal{{ $appointment->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Update Modal -->
                                    <div class="modal fade" id="updateModal{{ $appointment->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('appointments.update', $appointment) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Update Appointment</h5>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Status</label>
                                                            <select name="status" class="form-control" required>
                                                                @foreach($statuses as $status)
                                                                    <option value="{{ $status }}" 
                                                                            {{ $appointment->status == $status ? 'selected' : '' }}>
                                                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Notes</label>
                                                            <textarea name="notes" class="form-control" rows="3">{{ $appointment->notes }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $appointment->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('appointments.destroy', $appointment) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Delete Appointment</h5>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete this appointment?</p>
                                                        <p><strong>Patient:</strong> {{ $appointment->patient->full_name }}</p>
                                                        <p><strong>Date:</strong> {{ $appointment->appointment_date->format('M d, Y') }}</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No appointments found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $appointments->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
