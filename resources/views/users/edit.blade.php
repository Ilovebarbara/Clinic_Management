@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit User: {{ $user->name }}</h4>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data">
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">Role <span class="text-danger">*</span></label>
                                    <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role }}" 
                                                    {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $role)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="profile_image">Profile Image</label>
                                    @if($user->profile_image)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $user->profile_image) }}" 
                                                 alt="{{ $user->name }}" 
                                                 class="rounded-circle" 
                                                 width="60" height="60"
                                                 id="current-image">
                                        </div>
                                    @endif
                                    <input type="file" name="profile_image" id="profile_image" 
                                           class="form-control-file @error('profile_image') is-invalid @enderror" 
                                           accept="image/*">
                                    @error('profile_image')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Max size: 2MB. Supported formats: JPG, PNG</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active" 
                                       name="is_active" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    Active User
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update User</button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>

                    <!-- Change Password Section -->
                    <hr class="my-4">
                    <h5>Change Password</h5>
                    <form method="POST" action="{{ route('users.update', $user) }}" id="password-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="change_password" value="1">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <input type="password" name="password" id="new_password" 
                                           class="form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Leave blank to keep current password</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="form-control">
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
    // Preview profile image
    const imageInput = document.getElementById('profile_image');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const currentImage = document.getElementById('current-image');
                if (currentImage) {
                    currentImage.src = e.target.result;
                } else {
                    // Create preview if it doesn't exist
                    const preview = document.createElement('img');
                    preview.id = 'current-image';
                    preview.className = 'rounded-circle mb-2';
                    preview.style.width = '60px';
                    preview.style.height = '60px';
                    preview.style.objectFit = 'cover';
                    preview.src = e.target.result;
                    imageInput.parentNode.insertBefore(preview, imageInput);
                }
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endsection
