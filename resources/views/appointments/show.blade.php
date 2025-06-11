@extends('layouts.app')

@section('title', 'Appointment Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Appointment Details</h4>
                    <div>
                        <a href="{{ route('appointments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        @if($appointment->status !== 'completed')
                            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#updateModal">
                                <i class="fas fa-edit"></i> Update Status
                            </button>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Appointment Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Appointment ID</th>
                                    <td>{{ $appointment->id }}</td>
                                </tr>
                                <tr>
                                    <th>Date & Time</th>
                                    <td>
                                        {{ $appointment->appointment_date->format('F d, Y') }}<br>
                                        <strong>{{ $appointment->appointment_time->format('h:i A') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge badge-{{ $appointment->status_color }} badge-lg">
                                            {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Purpose</th>
                                    <td>{{ $appointment->purpose }}</td>
                                </tr>
                                @if($appointment->notes)
                                <tr>
                                    <th>Notes</th>
                                    <td>{{ $appointment->notes }}</td>
                                </tr>
                                @endif
                                @if($appointment->completed_at)
                                <tr>
                                    <th>Completed At</th>
                                    <td>{{ $appointment->completed_at->format('F d, Y h:i A') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Patient Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Name</th>
                                    <td>{{ $appointment->patient->full_name }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>{{ ucfirst($appointment->patient->patient_type) }}</td>
                                </tr>
                                @if($appointment->patient->student_id)
                                <tr>
                                    <th>Student ID</th>
                                    <td>{{ $appointment->patient->student_id }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Contact</th>
                                    <td>
                                        {{ $appointment->patient->phone }}<br>
                                        {{ $appointment->patient->email }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Age</th>
                                    <td>{{ $appointment->patient->age }} years old</td>
                                </tr>
                                <tr>
                                    <th>Blood Type</th>
                                    <td>{{ $appointment->patient->blood_type }}</td>
                                </tr>
                            </table>
                            
                            <h5 class="mt-4">Doctor Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Name</th>
                                    <td>{{ $appointment->doctor->name }}</td>
                                </tr>
                                <tr>
                                    <th>Specialization</th>
                                    <td>{{ $appointment->doctor->specialization }}</td>
                                </tr>
                                <tr>
                                    <th>License Number</th>
                                    <td>{{ $appointment->doctor->license_number }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($appointment->medicalRecords->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Medical Records</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Vitals</th>
                                            <th>Diagnosis</th>
                                            <th>Treatment</th>
                                            <th>Prescription</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($appointment->medicalRecords as $record)
                                        <tr>
                                            <td>{{ $record->created_at->format('M d, Y') }}</td>
                                            <td>
                                                @if($record->weight || $record->height || $record->blood_pressure)
                                                    Weight: {{ $record->weight }}kg<br>
                                                    Height: {{ $record->height }}cm<br>
                                                    BP: {{ $record->blood_pressure }}<br>
                                                    Temp: {{ $record->temperature }}°C
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $record->diagnosis }}</td>
                                            <td>{{ $record->treatment }}</td>
                                            <td>{{ $record->prescription ?: 'None' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
@if($appointment->status !== 'completed')
<div class="modal fade" id="updateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('appointments.update', $appointment) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Update Appointment Status</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="scheduled" {{ $appointment->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="in_progress" {{ $appointment->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $appointment->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="no_show" {{ $appointment->status == 'no_show' ? 'selected' : '' }}>No Show</option>
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
@endif
@endsection
