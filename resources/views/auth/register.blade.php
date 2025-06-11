<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .register-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .register-header {
            background: linear-gradient(45deg, #ef4444, #dc2626);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .form-section {
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 20px;
            padding-bottom: 20px;
        }
        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .section-title {
            color: #374151;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .section-title i {
            margin-right: 10px;
            width: 20px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h2 class="mb-2">
                    <i class="fas fa-user-plus me-2"></i>
                    Patient Registration
                </h2>
                <p class="mb-0 opacity-75">Create your account to access online services</p>
            </div>
            
            <div class="p-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Please correct the following errors:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('auth.register') }}">
                    @csrf
                    
                    <!-- Personal Information -->
                    <div class="form-section">
                        <h6 class="section-title">
                            <i class="fas fa-user"></i>
                            Personal Information
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" 
                                           value="{{ old('first_name') }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                                           value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                           value="{{ old('date_of_birth') }}" required>
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Blood Type <span class="text-danger">*</span></label>
                                    <select name="blood_type" class="form-select @error('blood_type') is-invalid @enderror" required>
                                        <option value="">Select Blood Type</option>
                                        <option value="A+" {{ old('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                                        <option value="A-" {{ old('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                                        <option value="B+" {{ old('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                                        <option value="B-" {{ old('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                                        <option value="AB+" {{ old('blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                        <option value="AB-" {{ old('blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                        <option value="O+" {{ old('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                                        <option value="O-" {{ old('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
                                    </select>
                                    @error('blood_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="form-section">
                        <h6 class="section-title">
                            <i class="fas fa-phone"></i>
                            Contact Information
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" 
                                      rows="2" required>{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- University Information -->
                    <div class="form-section">
                        <h6 class="section-title">
                            <i class="fas fa-university"></i>
                            University Information
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Patient Type <span class="text-danger">*</span></label>
                                    <select name="patient_type" class="form-select @error('patient_type') is-invalid @enderror" required>
                                        <option value="">Select Type</option>
                                        <option value="student" {{ old('patient_type') == 'student' ? 'selected' : '' }}>Student</option>
                                        <option value="faculty" {{ old('patient_type') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                                        <option value="personnel" {{ old('patient_type') == 'personnel' ? 'selected' : '' }}>Personnel</option>
                                        <option value="non_academic" {{ old('patient_type') == 'non_academic' ? 'selected' : '' }}>Non-Academic</option>
                                    </select>
                                    @error('patient_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Student/Employee ID</label>
                                    <input type="text" name="student_id" class="form-control @error('student_id') is-invalid @enderror" 
                                           value="{{ old('student_id') }}">
                                    @error('student_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Leave blank if not applicable</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Campus <span class="text-danger">*</span></label>
                                    <input type="text" name="campus" class="form-control @error('campus') is-invalid @enderror" 
                                           value="{{ old('campus') }}" required>
                                    @error('campus')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">College/Department <span class="text-danger">*</span></label>
                                    <input type="text" name="college" class="form-control @error('college') is-invalid @enderror" 
                                           value="{{ old('college') }}" required>
                                    @error('college')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="form-section">
                        <h6 class="section-title">
                            <i class="fas fa-exclamation-triangle"></i>
                            Emergency Contact
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Contact Name <span class="text-danger">*</span></label>
                                    <input type="text" name="emergency_contact_name" class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                           value="{{ old('emergency_contact_name') }}" required>
                                    @error('emergency_contact_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Contact Phone <span class="text-danger">*</span></label>
                                    <input type="tel" name="emergency_contact_phone" class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                                           value="{{ old('emergency_contact_phone') }}" required>
                                    @error('emergency_contact_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Information -->
                    <div class="form-section">
                        <h6 class="section-title">
                            <i class="fas fa-notes-medical"></i>
                            Medical Information
                        </h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Known Allergies</label>
                            <textarea name="allergies" class="form-control @error('allergies') is-invalid @enderror" 
                                      rows="3" placeholder="List any known allergies, medications, or medical conditions...">{{ old('allergies') }}</textarea>
                            @error('allergies')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">This information helps our medical staff provide better care</small>
                        </div>
                    </div>

                    <!-- Account Security -->
                    <div class="form-section">
                        <h6 class="section-title">
                            <i class="fas fa-lock"></i>
                            Account Security
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Minimum 8 characters</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger btn-lg">
                            <i class="fas fa-user-plus me-2"></i>
                            Create Account
                        </button>
                        <a href="{{ route('mobile.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to Home
                        </a>
                    </div>

                    <p class="text-center mt-3 small text-muted">
                        Already have an account? 
                        <a href="{{ route('mobile.index') }}" class="text-decoration-none">Login here</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show/hide student ID field based on patient type
        document.querySelector('select[name="patient_type"]').addEventListener('change', function() {
            const studentIdField = document.querySelector('input[name="student_id"]');
            const studentIdLabel = studentIdField.previousElementSibling;
            
            if (this.value === 'student' || this.value === 'faculty' || this.value === 'personnel') {
                studentIdLabel.innerHTML = 'Student/Employee ID <span class="text-danger">*</span>';
                studentIdField.required = true;
            } else {
                studentIdLabel.innerHTML = 'Student/Employee ID';
                studentIdField.required = false;
            }
        });
    </script>
</body>
</html>
