<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>レポート管理システム</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
    <style>
    /* Comprehensive Mobile Responsive Styles */
    
    /* Base Mobile Optimizations */
    @media (max-width: 576px) {
        body {
            font-size: 14px;
        }
        
        .container {
            padding-left: 15px;
            padding-right: 15px;
        }
        
        /* Prevent zoom on input focus (iOS) */
        input[type="text"], 
        input[type="email"], 
        input[type="password"], 
        input[type="tel"], 
        input[type="number"],
        input[type="datetime-local"],
        select,
        textarea {
            font-size: 16px !important;
        }
        
        /* Better touch targets */
        .btn {
            min-height: 44px;
            padding: 0.75rem 1rem;
        }
        
        .btn-sm {
            min-height: 38px;
            padding: 0.5rem 0.75rem;
        }
        
        /* Improved form controls */
        .form-control,
        .form-select {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
        }
        
        /* Better card spacing */
        .card-body {
            padding: 1.5rem 1rem !important;
        }
        
        /* Improved table responsiveness */
        .table-responsive {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        /* Better modal handling */
        .modal-dialog {
            margin: 1rem;
        }
        
        /* Improved navbar */
        .navbar-brand {
            font-size: 1.1rem;
        }
        
        .navbar-nav .nav-link {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        
        .navbar-nav .nav-link:last-child {
            border-bottom: none;
        }
        
        /* Better dropdown menus */
        .dropdown-menu {
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }
        
        .dropdown-item {
            padding: 0.75rem 1rem;
        }
        
        /* Improved alerts */
        .alert {
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        
        /* Better spacing for mobile */
        .py-4 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }
        
        .mb-4 {
            margin-bottom: 1.5rem !important;
        }
        
        /* Improved grid system */
        .row {
            margin-left: -0.5rem;
            margin-right: -0.5rem;
        }
        
        .col, .col-1, .col-2, .col-3, .col-4, .col-5, .col-6, 
        .col-7, .col-8, .col-9, .col-10, .col-11, .col-12 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
    }
    
    /* Tablet Optimizations */
    @media (min-width: 577px) and (max-width: 768px) {
        .container {
            max-width: 100%;
        }
        
        .btn {
            min-height: 40px;
        }
        
        .form-control,
        .form-select {
            padding: 0.625rem 0.875rem;
        }
    }
    
    /* Enhanced Touch Interactions */
    .btn, .nav-link, .dropdown-item {
        transition: all 0.2s ease;
    }
    
    .btn:active, .nav-link:active, .dropdown-item:active {
        transform: scale(0.98);
    }
    
    /* Improved Focus States */
    .btn:focus, .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    /* Better Loading States */
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    /* Enhanced Card Design */
    .card {
        border-radius: 1rem;
        box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    }
    
    /* Improved Table Design */
    .table {
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .table th {
        background-color: #f8f9fa;
        border-top: none;
        font-weight: 600;
        color: #495057;
    }
    
    /* Better Form Design */
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .form-control, .form-select {
        border-radius: 0.5rem;
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    /* Enhanced Button Design */
    .btn-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-secondary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4);
    }
    
    /* Improved Badge Design */
    .badge {
        border-radius: 0.5rem;
        font-weight: 500;
    }
    
    /* Better Modal Design */
    .modal-content {
        border-radius: 1rem;
        border: none;
        box-shadow: 0 1rem 3rem rgba(0,0,0,0.2);
    }
    
    .modal-header {
        border-bottom: 1px solid #dee2e6;
        border-radius: 1rem 1rem 0 0;
    }
    
    .modal-footer {
        border-top: 1px solid #dee2e6;
        border-radius: 0 0 1rem 1rem;
    }
    
    /* Enhanced Animation Classes */
    .animate-slide-up {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease;
    }
    
    .animate-slide-up.animate-in {
        opacity: 1;
        transform: translateY(0);
    }
    
    .animate-fade-in {
        opacity: 0;
        transition: all 0.5s ease;
    }
    
    .animate-fade-in.animate-in {
        opacity: 1;
    }
    
    /* Improved Notification System */
    .notification-item {
        border-radius: 0.75rem;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        margin-bottom: 0.5rem;
        animation: slideInRight 0.3s ease;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    /* Better Loading States */
    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Improved Accessibility */
    .btn:focus,
    .nav-link:focus,
    .dropdown-item:focus {
        outline: 2px solid #0d6efd;
        outline-offset: 2px;
    }
    
    /* Better Print Styles */
    @media print {
        .navbar,
        .btn,
        .modal {
            display: none !important;
        }
        
        .card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
        }
    }
    
    /* Site Content Margins */
    .site-content {
        margin-left: 100px;
        margin-right: 100px;
        transition: all 0.3s ease;
    }
    
    /* Responsive margins for smaller screens */
    @media (max-width: 1200px) {
        .site-content {
            margin-left: 50px;
            margin-right: 50px;
        }
    }
    
    @media (max-width: 768px) {
        .site-content {
            margin-left: 20px;
            margin-right: 20px;
        }
    }
    
    @media (max-width: 576px) {
        .site-content {
            margin-left: 15px;
            margin-right: 15px;
        }
    }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-3">
            <div class="container">
                <a class="navbar-brand fw-bold" href="/">
                    <i class="fas fa-clipboard-list me-2"></i>
                    <span class="d-none d-sm-inline">レポート管理システム</span>
                    <span class="d-inline d-sm-none">レポート管理</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="メニュー表示">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                @if(auth()->user()->role !== 'admin')
                                    <a class="nav-link" href="{{ route('showForm') }}">
                                        <i class="fas fa-plus me-1 d-lg-none"></i>
                                        <span class="d-none d-lg-inline">新規レポート</span>
                                        <span class="d-inline d-lg-none">新規作成</span>
                                    </a>
                                @endif
                            </li>
                            @if(auth()->user()->role === 'admin')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-1 d-lg-none"></i>
                                        <span class="d-none d-lg-inline">管理ダッシュボード</span>
                                        <span class="d-inline d-lg-none">ダッシュボード</span>
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">
                                        <i class="fas fa-sign-in-alt me-1 d-lg-none"></i>
                                        <span class="d-none d-lg-inline">ログイン</span>
                                        <span class="d-inline d-lg-none">ログイン</span>
                                    </a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">
                                        <i class="fas fa-user-plus me-1 d-lg-none"></i>
                                        <span class="d-none d-lg-inline">新規登録</span>
                                        <span class="d-inline d-lg-none">登録</span>
                                    </a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fas fa-user-circle me-1 d-lg-none"></i>
                                    <span class="d-none d-lg-inline">{{ Auth::user()->name }}</span>
                                    <span class="d-inline d-lg-none">{{ Str::limit(Auth::user()->name, 10) }}</span>
                                    @if(auth()->user()->role === 'admin')
                                        <span class="badge bg-danger ms-1">管理者</span>
                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <div class="dropdown-header">
                                        <small class="text-muted">{{ Auth::user()->email }}</small>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('owner.edit') }}">
                                        <i class="fas fa-user-edit me-2"></i>オーナー情報編集
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>ログアウト
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <main class="py-2">
            @if(session('success'))
                <div class="container">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="閉じる"></button>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="container">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="閉じる"></button>
                    </div>
                </div>
            @endif
            @if(session('warning'))
                <div class="container">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="閉じる"></button>
                    </div>
                </div>
            @endif
            <div class="site-content">
                @yield('content')
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
