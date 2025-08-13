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

                    <!-- Header -->
                    <div class="form-header mb-4">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0 text-muted">
                                    <i class="fas fa-user-plus me-2"></i>アカウント登録
                                </h5>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">

                        <div class="d-flex align-items-center gap-3 avatar-row">
                            <!-- <label for="avatar" class="avatar-preview-label" title="クリックして画像を選択">
                                <img id="avatar-preview" src="{{ asset('images/default-avatar.png') }}" alt="Avatar Preview" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 2px solid #e9ecef; cursor: pointer;">
                            </label> -->
                            <div id="avatar-input-container" class="flex-grow-1 d-none">
                                <input id="avatar" type="file" class="form-control form-control-lg @error('avatar') is-invalid @enderror" name="avatar" accept="image/*">
                                @error('avatar')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
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
                        <div class="input-group input-group-lg">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="パスワードを入力">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password"><i class="fas fa-eye"></i></button>
                        </div>
                        @error('password')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password-confirm" class="form-label">
                            <i class="fas fa-lock me-1"></i>{{ __('Confirm Password') }}
                        </label>
                        <div class="input-group input-group-lg">
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="パスワードを再入力">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password-confirm"><i class="fas fa-eye"></i></button>
                        </div>
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
    0% { left: -100%; }
    50% { left: 100%; }
    100% { left: 100%; }
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
    0% { opacity: 0; transform: translateY(10px); }
    100% { opacity: 1; transform: translateY(0); }
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
    0% { opacity: 0; transform: translateY(10px); }
    100% { opacity: 1; transform: translateY(0); }
}

/* Form Header Styling */
.form-header { border-bottom: 1px solid #e9ecef; padding-bottom: 1rem; }

/* Responsive Design */
@media (max-width: 576px) {
    .login-container { padding: 15px; }
    .login-card-wrapper { max-width: 100%; }
    .login-card .card-body { padding: 1.5rem 1rem !important; }
    .form-control-lg { font-size: 16px !important; /* Prevents zoom on iOS */ }
    .btn-lg { padding: 0.75rem 1.5rem; font-size: 1rem; }
    .form-header .row { flex-direction: column; text-align: center; }
}

/* Enhanced Card Styling */
.login-card { backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95); }
.login-card:hover { transform: translateY(-5px); box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4); transition: all 0.3s ease; }

/* Make label behave like a button for avatar */
.avatar-preview-label { display: inline-block; margin: 0; }
.avatar-preview-label img { display: block; }

/* Mobile responsive adjustments */
@media (max-width: 576px) {
  .avatar-row { flex-direction: column; align-items: flex-start !important; }
  .avatar-preview-label img { width: 72px; height: 72px; }
  .login-card .form-control, .login-card .btn { font-size: 16px; } /* avoid iOS zoom */
  .form-header { text-align: left; }
}

/* Improve spacing on small screens */
@media (max-width: 768px) {
  .login-container { margin-top: 20px; }
  .login-card .card-body { padding: 1.25rem !important; }
  .mb-3, .mb-4 { margin-bottom: 0.9rem !important; }
}
</style>
 @endpush

@push('scripts')
<script>
// Avatar preview and input toggle
const avatarInput = document.getElementById('avatar');
const avatarPreview = document.getElementById('avatar-preview');
const avatarInputContainer = document.getElementById('avatar-input-container');
if (avatarInput && avatarPreview && avatarInputContainer) {
    // Clicking preview triggers file dialog without showing the input
    avatarPreview.addEventListener('click', function() {
        avatarInput.click();
    });

    avatarInput.addEventListener('change', function() {
        const file = this.files && this.files[0];
        if (!file) return;

        // Validate on client side (optional, server still validates)
        const maxBytes = 2 * 1024 * 1024; // 2MB
        if (!/^image\/(jpeg|png|gif)$/.test(file.type)) {
            alert('JPG/PNG/GIF の画像を選択してください');
            this.value = '';
            return;
        }
        if (file.size > maxBytes) {
            alert('ファイルサイズは2MB以下にしてください');
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            avatarPreview.src = e.target.result;
            // Keep file input hidden
            avatarInputContainer.classList.add('d-none');
        };
        reader.readAsDataURL(file);
    });
}

// Password visibility toggles
for (const btn of document.querySelectorAll('.toggle-password')) {
    btn.addEventListener('click', function(){
        const targetId = this.getAttribute('data-target');
        const input = document.getElementById(targetId);
        if (!input) return;
        const showing = input.getAttribute('type') === 'text';
        input.setAttribute('type', showing ? 'password' : 'text');
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
}
</script>
@endpush
