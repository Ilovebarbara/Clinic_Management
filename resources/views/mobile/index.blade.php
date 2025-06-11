<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Portal - {{ config('app.name') }}</title>
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
            padding: 30px 20px;
            text-align: center;
        }
        .service-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .service-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin: 0 auto 15px;
        }
        .queue-status-card {
            background: linear-gradient(45deg, #10b981, #059669);
        }
        .appointment-card {
            background: linear-gradient(45deg, #f59e0b, #d97706);
        }
        .login-card {
            background: linear-gradient(45deg, #8b5cf6, #7c3aed);
        }
        .register-card {
            background: linear-gradient(45deg, #ef4444, #dc2626);
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <div class="mobile-header">
            <h2 class="mb-2">
                <i class="fas fa-hospital-alt me-2"></i>
                University Clinic
            </h2>
            <p class="mb-0 opacity-75">Mobile Portal</p>
        </div>
        
        <div class="p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Queue Status -->
            <div class="card service-card">
                <div class="card-body text-center p-4">
                    <div class="service-icon queue-status-card">
                        <i class="fas fa-list-ol"></i>
                    </div>
                    <h5 class="card-title">Queue Status</h5>
                    <p class="text-muted">Check current queue and your position</p>
                    <a href="{{ route('mobile.queue-status') }}" class="btn btn-success">
                        <i class="fas fa-eye me-2"></i>View Queue
                    </a>
                </div>
            </div>

            <!-- Book Appointment -->
            <div class="card service-card">
                <div class="card-body text-center p-4">
                    <div class="service-icon appointment-card">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h5 class="card-title">Book Appointment</h5>
                    <p class="text-muted">Schedule your visit in advance</p>
                    <button class="btn btn-warning" onclick="showLoginPrompt()">
                        <i class="fas fa-calendar-plus me-2"></i>Book Now
                    </button>
                </div>
            </div>

            <!-- Patient Login -->
            <div class="card service-card">
                <div class="card-body text-center p-4">
                    <div class="service-icon login-card">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <h5 class="card-title">Patient Login</h5>
                    <p class="text-muted">Access your medical records and appointments</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </div>
            </div>

            <!-- Register -->
            <div class="card service-card">
                <div class="card-body text-center p-4">
                    <div class="service-icon register-card">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h5 class="card-title">New Patient Registration</h5>
                    <p class="text-muted">Create your account for online services</p>
                    <a href="{{ route('auth.register') }}" class="btn btn-danger">
                        <i class="fas fa-user-plus me-2"></i>Register
                    </a>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="alert alert-danger mt-4">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Emergency</h6>
                <p class="mb-2">For medical emergencies, please call:</p>
                <h5><i class="fas fa-phone me-2"></i>(123) 456-7890</h5>
            </div>

            <!-- Clinic Information -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Clinic Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <strong>Hours:</strong><br>
                            <small>
                                Mon-Fri: 8AM-5PM<br>
                                Saturday: 8AM-12PM
                            </small>
                        </div>
                        <div class="col-6">
                            <strong>Location:</strong><br>
                            <small>
                                University Campus<br>
                                Building A, 2nd Floor
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Patient Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <input type="hidden" name="user_type" value="patient">
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Email or Student ID</label>
                            <input type="text" name="login" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showLoginPrompt() {
            if (confirm('You need to login to book an appointment. Would you like to login now?')) {
                var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
            }
        }
        
        // Check if user is logged in (you can implement this based on your session logic)
        function checkLoginStatus() {
            // This would be implemented based on your authentication system
            return false; // For now, assume not logged in
        }
    </script>
</body>
</html>
