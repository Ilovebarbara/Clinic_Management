@extends('layouts.app')

@section('title', 'Welcome - University Clinic Management System')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="row align-items-center py-5">
        <div class="col-lg-6">
            <div class="pe-lg-5">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    University Clinic Management System
                </h1>
                <p class="lead text-muted mb-4">
                    Modern, efficient healthcare management for university communities. 
                    Streamlined patient care, appointment scheduling, and medical record management.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Staff Login
                    </a>
                    <a href="{{ route('patient.login') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-user me-2"></i>Patient Portal
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="text-center">
                <i class="fas fa-hospital-alt" style="font-size: 12rem; color: var(--primary-color); opacity: 0.1;"></i>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="row py-5">
        <div class="col-12">
            <h2 class="text-center mb-5">System Features</h2>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-users fa-3x text-primary"></i>
                    </div>
                    <h5 class="card-title">Patient Management</h5>
                    <p class="card-text text-muted">
                        Comprehensive patient records, registration, and profile management for students, faculty, and staff.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-calendar-check fa-3x text-success"></i>
                    </div>
                    <h5 class="card-title">Appointment System</h5>
                    <p class="card-text text-muted">
                        Online appointment booking, scheduling management, and automated reminders for patients and staff.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-list-ol fa-3x text-warning"></i>
                    </div>
                    <h5 class="card-title">Queue Management</h5>
                    <p class="card-text text-muted">
                        Smart queuing system with priority levels, kiosk integration, and real-time status updates.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-file-medical fa-3x text-info"></i>
                    </div>
                    <h5 class="card-title">Medical Records</h5>
                    <p class="card-text text-muted">
                        Digital medical records, consultation history, and secure document management system.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-mobile-alt fa-3x text-purple"></i>
                    </div>
                    <h5 class="card-title">Mobile Access</h5>
                    <p class="card-text text-muted">
                        Responsive design for mobile access, patient portal, and real-time notifications.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-chart-bar fa-3x text-secondary"></i>
                    </div>
                    <h5 class="card-title">Analytics & Reports</h5>
                    <p class="card-text text-muted">
                        Comprehensive reporting, analytics dashboard, and performance insights for clinic management.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Section -->
    <div class="row py-5 bg-light rounded">
        <div class="col-12">
            <h3 class="text-center mb-4">Quick Access</h3>
        </div>
        <div class="col-md-6 text-center mb-3">
            <div class="card border-primary">
                <div class="card-body">
                    <i class="fas fa-user-md fa-3x text-primary mb-3"></i>
                    <h5>Healthcare Staff</h5>
                    <p class="text-muted">Access patient records, manage appointments, and update medical information.</p>
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Staff Login
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-center mb-3">
            <div class="card border-success">
                <div class="card-body">
                    <i class="fas fa-user fa-3x text-success mb-3"></i>
                    <h5>Students & Faculty</h5>
                    <p class="text-muted">View medical records, book appointments, and manage your health information.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('patient.login') }}" class="btn btn-success">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                        <a href="{{ route('patient.register') }}" class="btn btn-outline-success">
                            <i class="fas fa-user-plus me-2"></i>Register
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="row py-4">
        <div class="col-12">
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="fas fa-check-circle fa-2x me-3"></i>
                <div>
                    <h6 class="mb-1">System Status: Online</h6>
                    <small class="mb-0">All clinic services are operational. Last updated: {{ now()->format('M d, Y h:i A') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .text-purple {
        color: #7c3aed !important;
    }
    
    .hero-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .feature-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 2rem;
    }
</style>
@endpush
