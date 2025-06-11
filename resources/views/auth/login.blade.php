@extends('layouts.app')

@section('title', 'Staff Login - Clinic Management System')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0" style="margin-top: 5rem;">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-md fa-3x text-primary mb-3"></i>
                        <h3 class="h4 font-weight-bold">Staff Login</h3>
                        <p class="text-muted">Sign in to access the clinic management system</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="login" class="form-label">Username or Email</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input id="login" type="text" class="form-control @error('login') is-invalid @enderror" 
                                       name="login" value="{{ old('login') }}" required autocomplete="username" autofocus
                                       placeholder="Enter username or email">
                            </div>
                            @error('login')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required autocomplete="current-password"
                                       placeholder="Enter password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-2">
                            <a href="{{ route('password.request') }}" class="text-decoration-none">
                                <i class="fas fa-question-circle me-1"></i>Forgot your password?
                            </a>
                        </p>
                        <p class="mb-0">
                            <a href="{{ route('patient.login') }}" class="text-decoration-none">
                                <i class="fas fa-user me-1"></i>Patient Login
                            </a>
                        </p>
                    </div>
                </div>

                <div class="card-footer bg-light text-center">
                    <small class="text-muted">
                        University Clinic Management System<br>
                        For technical support, contact IT Department
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Demo Credentials Card -->
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Demo Credentials
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <strong>Super Admin:</strong><br>
                            <code>admin / admin123</code>
                        </div>
                        <div class="col-6">
                            <strong>Doctor:</strong><br>
                            <code>dr.smith / doctor123</code>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <strong>Nurse:</strong><br>
                            <code>nurse.jane / nurse123</code>
                        </div>
                        <div class="col-6">
                            <strong>Reception:</strong><br>
                            <code>reception.mary / reception123</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordField = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Demo credential quick fill
    document.addEventListener('click', function(e) {
        if (e.target.tagName === 'CODE') {
            const credentials = e.target.textContent.split(' / ');
            if (credentials.length === 2) {
                document.getElementById('login').value = credentials[0];
                document.getElementById('password').value = credentials[1];
                
                // Add visual feedback
                e.target.style.backgroundColor = '#d4edda';
                setTimeout(() => {
                    e.target.style.backgroundColor = '';
                }, 1000);
            }
        }
    });
</script>
@endpush
