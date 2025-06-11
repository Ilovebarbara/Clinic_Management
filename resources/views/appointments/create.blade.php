@extends('layouts.app')

@section('title', 'Create Appointment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Create New Appointment</h4>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('appointments.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="patient_id">Patient <span class="text-danger">*</span></label>
                                    <select name="patient_id" id="patient_id" class="form-control @error('patient_id') is-invalid @enderror" required>
                                        <option value="">Select Patient</option>
                                        @foreach($patients as $patient)
                                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                                {{ $patient->full_name }} 
                                                @if($patient->student_id)
                                                    ({{ $patient->student_id }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('patient_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="doctor_id">Doctor <span class="text-danger">*</span></label>
                                    <select name="doctor_id" id="doctor_id" class="form-control @error('doctor_id') is-invalid @enderror" required>
                                        <option value="">Select Doctor</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                                {{ $doctor->name }} - {{ $doctor->specialization }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('doctor_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointment_date">Appointment Date <span class="text-danger">*</span></label>
                                    <input type="date" name="appointment_date" id="appointment_date" 
                                           class="form-control @error('appointment_date') is-invalid @enderror" 
                                           value="{{ old('appointment_date') }}" 
                                           min="{{ date('Y-m-d') }}" required>
                                    @error('appointment_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointment_time">Appointment Time <span class="text-danger">*</span></label>
                                    <select name="appointment_time" id="appointment_time" 
                                            class="form-control @error('appointment_time') is-invalid @enderror" required>
                                        <option value="">Select Time</option>
                                        <!-- Time slots will be populated by JavaScript -->
                                    </select>
                                    @error('appointment_time')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="purpose">Purpose <span class="text-danger">*</span></label>
                            <textarea name="purpose" id="purpose" 
                                      class="form-control @error('purpose') is-invalid @enderror" 
                                      rows="3" required>{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" 
                                      class="form-control @error('notes') is-invalid @enderror" 
                                      rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Create Appointment</button>
                            <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const doctorSelect = document.getElementById('doctor_id');
    const dateInput = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');

    function updateTimeSlots() {
        const doctorId = doctorSelect.value;
        const date = dateInput.value;

        if (!doctorId || !date) {
            timeSelect.innerHTML = '<option value="">Select Time</option>';
            return;
        }

        // Show loading
        timeSelect.innerHTML = '<option value="">Loading...</option>';

        fetch(`/appointments/check-availability?doctor_id=${doctorId}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                timeSelect.innerHTML = '<option value="">Select Time</option>';
                
                data.available_slots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = formatTime(slot);
                    timeSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                timeSelect.innerHTML = '<option value="">Error loading slots</option>';
            });
    }

    function formatTime(time) {
        const [hours, minutes] = time.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour % 12 || 12;
        return `${displayHour}:${minutes} ${ampm}`;
    }

    doctorSelect.addEventListener('change', updateTimeSlots);
    dateInput.addEventListener('change', updateTimeSlots);
});
</script>
@endsection
