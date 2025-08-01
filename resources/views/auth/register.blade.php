@extends('layouts.app')

@section('content')
<div class="login-container">
    <div class="login-card-wrapper">
        <div class="card login-card shadow-lg border-0">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0">
                    <i class="fas fa-user-plus me-2"></i>{{ __('Register') }}
                </h4>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Header with Avatar -->
                    <div class="form-header mb-4">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0 text-muted">
                                    <i class="fas fa-user-plus me-2"></i>アカウント登録
                                </h5>
                            </div>
                            <div class="col-auto">
                                <!-- Avatar Selection -->
                                <div class="avatar-selection-container">
                                    <div class="avatar-preview" id="avatar-preview" onclick="triggerFileUpload()">
                                        <img src="/images/default-avatar.png" alt="Default Avatar" id="preview-image" class="avatar-preview-img">
                                        <div class="avatar-preview-placeholder" id="preview-placeholder">
                                            <i class="fas fa-user fa-2x text-muted"></i>
                                        </div>
                                        <div class="avatar-upload-overlay">
                                            <i class="fas fa-camera fa-sm text-white"></i>
                                            <span class="upload-text">選択</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Hidden file input -->
                                <input id="avatar" type="file" class="d-none" name="avatar" accept="image/*" onchange="previewAvatar(this)">
                            </div>
                        </div>
                        
                        @error('avatar')
                            <span class="invalid-feedback d-block mt-2" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="fas fa-user me-1"></i>{{ __('Name') }}
                        </label>
                        <input id="name" type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="お名前を入力">

                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-1"></i>{{ __('Email Address') }}
                        </label>
                        <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="メールアドレスを入力">

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-1"></i>{{ __('Password') }}
                        </label>
                        <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="パスワードを入力">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password-confirm" class="form-label">
                            <i class="fas fa-lock me-1"></i>{{ __('Confirm Password') }}
                        </label>
                        <input id="password-confirm" type="password" class="form-control form-control-lg" name="password_confirmation" required autocomplete="new-password" placeholder="パスワードを再入力">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus me-2"></i>{{ __('Register') }}
                        </button>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-sign-in-alt me-2"></i>{{ __('Already have an account? Login') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewAvatar(input) {
    const preview = document.getElementById('preview-image');
    const placeholder = document.getElementById('preview-placeholder');
    const file = input.files[0];
    
    if (file) {
        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('ファイルサイズは2MB以下にしてください。');
            input.value = '';
            return;
        }
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('画像ファイルを選択してください。');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
        placeholder.style.display = 'flex';
    }
}

function triggerFileUpload() {
    document.getElementById('avatar').click();
}

// Initialize with default placeholder
document.addEventListener('DOMContentLoaded', function() {
    const preview = document.getElementById('preview-image');
    const placeholder = document.getElementById('preview-placeholder');
    
    // Show placeholder by default
    preview.style.display = 'none';
    placeholder.style.display = 'flex';
});
</script>
@endpush

@push('styles')
<style>
/* Login Container - Full Screen Centering */
.login-container {
    height: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    margin-top: 50px;
}

.login-card-wrapper {
    width: 100%;
    max-width: 500px;
    animation: loginCardAppear 0.8s ease-out 0.3s both;
}

.login-card {
    border-radius: 1.5rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    transform: scale(0.8);
    opacity: 0;
    animation: loginCardScale 0.6s ease-out 0.3s forwards;
}

/* Animation for card appearance */
@keyframes loginCardAppear {
    0% {
        opacity: 0;
        transform: translateY(30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Animation for card scaling */
@keyframes loginCardScale {
    0% {
        transform: scale(0.8);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Card Header Animation */
.login-card .card-header {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border: none;
    position: relative;
    overflow: hidden;
}

.login-card .card-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    animation: headerShine 2s ease-in-out 1s infinite;
}

@keyframes headerShine {
    0% {
        left: -100%;
    }
    50% {
        left: 100%;
    }
    100% {
        left: 100%;
    }
}

/* Form Input Animations */
.login-card .form-control {
    border-radius: 0.75rem;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    animation: inputAppear 0.5s ease-out 0.6s both;
}

.login-card .form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    transform: translateY(-2px);
}

@keyframes inputAppear {
    0% {
        opacity: 0;
        transform: translateY(10px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Button Animation */
.login-card .btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border: none;
    transition: all 0.3s ease;
    animation: buttonAppear 0.5s ease-out 0.9s both;
}

.login-card .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(13, 110, 253, 0.4);
}

@keyframes buttonAppear {
    0% {
        opacity: 0;
        transform: translateY(10px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Avatar Styling */
.form-header {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 1rem;
}

.avatar-selection-container {
    display: flex;
    justify-content: flex-end;
}

.avatar-preview {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 2px solid #e9ecef;
    overflow: hidden;
    position: relative;
    background: #f8f9fa;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    cursor: pointer;
}

.avatar-preview:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    border-color: #0d6efd;
}

.avatar-preview-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: none;
}

.avatar-preview-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
}

.avatar-upload-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.avatar-preview:hover .avatar-upload-overlay {
    opacity: 1;
}

.avatar-upload-overlay .upload-text {
    margin-top: 2px;
    font-size: 0.7rem;
    color: white;
    text-align: center;
    font-weight: 500;
}



/* Responsive Design */
@media (max-width: 576px) {
    .login-container {
        padding: 15px;
    }
    
    .login-card-wrapper {
        max-width: 100%;
    }
    
    .login-card .card-body {
        padding: 1.5rem 1rem !important;
    }
    
    .form-control-lg {
        font-size: 16px !important; /* Prevents zoom on iOS */
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
    
    .form-header .row {
        flex-direction: column;
        text-align: center;
    }
    
    .form-header .col-auto {
        margin-top: 1rem;
    }
    
    .avatar-preview {
        width: 50px;
        height: 50px;
    }
    
    .avatar-upload-overlay .upload-text {
        font-size: 0.6rem;
    }
}

/* Enhanced Card Styling */
.login-card {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
}

.login-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
    transition: all 0.3s ease;
}
</style>
@endpush
