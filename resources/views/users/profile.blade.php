@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    @if($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" 
                             alt="{{ $user->name }}" 
                             class="rounded-circle mb-3" 
                             width="120" height="120"
                             style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3" 
                             style="width: 120px; height: 120px; color: white; font-size: 48px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    
                    <h4>{{ $user->name }}</h4>
                    <p class="text-muted">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
                    
                    <form method="POST" action="{{ route('profile.image') }}" enctype="multipart/form-data" id="image-form">
                        @csrf
                        <input type="file" name="profile_image" id="profile_image" 
                               accept="image/*" style="display: none;">
                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                onclick="document.getElementById('profile_image').click()">
                            <i class="fas fa-camera"></i> Change Photo
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">Account Information</h6>
                </div>
                <div class="card-body">
                    <p><strong>Username:</strong> {{ $user->username }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Phone:</strong> {{ $user->phone ?: 'Not provided' }}</p>
                    <p><strong>Role:</strong> {{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
                    <p><strong>Member Since:</strong> {{ $user->created_at->format('F Y') }}</p>
                    <p><strong>Last Login:</strong> 
                        @if($user->last_login_at)
                            {{ $user->last_login_at->diffForHumans() }}
                        @else
                            Never
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" id="username" 
                                           class="form-control @error('username') is-invalid @enderror" 
                                           value="{{ old('username', $user->username) }}" required>
                                    @error('username')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" name="phone" id="phone" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="current_password">Current Password <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" id="current_password" 
                                   class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">New Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" id="password" 
                                           class="form-control @error('password') is-invalid @enderror" required>
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Minimum 8 characters</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm New Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-warning">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit image form when file is selected
    const imageInput = document.getElementById('profile_image');
    const imageForm = document.getElementById('image-form');
    
    imageInput.addEventListener('change', function() {
        if (this.files[0]) {
            imageForm.submit();
        }
    });
});
</script>
@endsection
