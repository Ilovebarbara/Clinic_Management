<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Kiosk - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .kiosk-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .welcome-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .welcome-header {
            background: linear-gradient(45deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .service-btn {
            width: 100%;
            height: 120px;
            border-radius: 15px;
            border: none;
            font-size: 1.2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        .service-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .btn-walk-in {
            background: linear-gradient(45deg, #10b981, #059669);
            color: white;
        }
        .btn-appointment {
            background: linear-gradient(45deg, #f59e0b, #d97706);
            color: white;
        }
        .clinic-hours {
            background: #f8fafc;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="kiosk-container">
        <div class="welcome-card">
            <div class="welcome-header">
                <h1 class="mb-3">
                    <i class="fas fa-hospital-alt me-3"></i>
                    University Clinic
                </h1>
                <p class="lead mb-0">Welcome! Please select your service type</p>
            </div>
            
            <div class="p-5">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <button type="button" class="service-btn btn-walk-in" data-bs-toggle="modal" data-bs-target="#walkInModal">
                            <i class="fas fa-walking fa-2x mb-3 d-block"></i>
                            Walk-in Service
                            <small class="d-block mt-2 opacity-75">Get a queue number</small>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="service-btn btn-appointment" data-bs-toggle="modal" data-bs-target="#appointmentModal">
                            <i class="fas fa-calendar-check fa-2x mb-3 d-block"></i>
                            Book Appointment
                            <small class="d-block mt-2 opacity-75">Schedule for later</small>
                        </button>
                    </div>
                </div>

                <div class="clinic-hours">
                    <h5 class="text-center mb-3">
                        <i class="fas fa-clock me-2"></i>
                        Clinic Hours
                    </h5>
                    <div class="row text-center">
                        <div class="col-6">
                            <strong>Monday - Friday</strong><br>
                            8:00 AM - 5:00 PM
                        </div>
                        <div class="col-6">
                            <strong>Saturday</strong><br>
                            8:00 AM - 12:00 PM
                        </div>
                    </div>
                    <p class="text-center mt-3 mb-0 text-muted">
                        <small>Closed on Sundays and Holidays</small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Walk-in Registration Modal -->
    <div class="modal fade" id="walkInModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-walking me-2"></i>
                        Walk-in Registration
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('kiosk.register') }}">
                    @csrf
                    <input type="hidden" name="service_type" value="walk_in">
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="date_of_birth" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select name="gender" class="form-select" required>
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Blood Type <span class="text-danger">*</span></label>
                                    <select name="blood_type" class="form-select" required>
                                        <option value="">Select Blood Type</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Patient Type <span class="text-danger">*</span></label>
                                    <select name="patient_type" class="form-select" required>
                                        <option value="">Select Type</option>
                                        <option value="student">Student</option>
                                        <option value="faculty">Faculty</option>
                                        <option value="personnel">Personnel</option>
                                        <option value="non_academic">Non-Academic</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Student ID (if applicable)</label>
                                    <input type="text" name="student_id" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Campus <span class="text-danger">*</span></label>
                                    <input type="text" name="campus" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">College <span class="text-danger">*</span></label>
                                    <input type="text" name="college" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control" rows="2" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Emergency Contact Name <span class="text-danger">*</span></label>
                                    <input type="text" name="emergency_contact_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Emergency Contact Phone <span class="text-danger">*</span></label>
                                    <input type="tel" name="emergency_contact_phone" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Allergies (if any)</label>
                            <textarea name="allergies" class="form-control" rows="2" placeholder="List any known allergies..."></textarea>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-ticket-alt me-2"></i>
                            Get Queue Number
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Appointment Modal -->
    <div class="modal fade" id="appointmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-check me-2"></i>
                        Book Appointment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-5">
                    <i class="fas fa-mobile-alt fa-4x text-primary mb-4"></i>
                    <h4>Book Online</h4>
                    <p class="text-muted mb-4">
                        To book an appointment, please visit our online portal or use your mobile device.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('mobile.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-external-link-alt me-2"></i>
                            Go to Online Portal
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
