<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0d6efd">
    <title>レポート管理システム</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
    <script>
      // Enable lightweight mode on touch devices or when reduced motion is preferred
      (function(){
        try {
          var lite = window.matchMedia('(pointer: coarse)').matches || window.matchMedia('(prefers-reduced-motion: reduce)').matches;
          if (lite) document.documentElement.classList.add('mobile-lite');
        } catch(e) {}
      })();
    </script>
    <style>
    /* Lightweight mobile mode: reduce animations/effects for speed */
    .mobile-lite .animate-slide-up,
    .mobile-lite .animate-fade-in,
    .mobile-lite .btn-animated,
    .mobile-lite .card,
    .mobile-lite .table,
    .mobile-lite .notification-alert {
        transition: none !important;
        animation: none !important;
    }
    .mobile-lite .card:hover,
    .mobile-lite .btn-animated:hover {
        transform: none !important;
        box-shadow: none !important;
    }
    .mobile-lite .card,
    .mobile-lite .table,
    .mobile-lite .modal-content {
        box-shadow: 0 1px 3px rgba(0,0,0,0.08) !important;
    }
    .mobile-lite .blue-button:hover {
        transform: translateX(-50%) !important;
        box-shadow: 0 6px 18px rgba(0,123,255,0.25) !important;
    }
    </style>
    <style>
    /* Avatar Display Styles */
    .user-avatar {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    
    .user-avatar img {
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
        object-fit: cover;
        object-position: center;
        display: block;
        width: 100%;
        height: 100%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .user-avatar img:hover {
        border-color: #007bff;
        transform: scale(1.05);
    }
    
    .user-avatar img.rounded-circle {
        border-radius: 50%;
    }
    
    /* Ensure avatars load properly */
    .user-avatar img[src*="default-avatar.png"] {
        background-color: #e9ecef;
    }
    
    /* Loading state for avatars */
    .user-avatar img:not([src]) {
        background-color: #f8f9fa;
        border: 2px dashed #dee2e6;
    }
    
    /* Specific avatar sizes */
    .user-avatar-img {
        width: 32px !important;
        height: 32px !important;
        min-width: 32px;
        min-height: 32px;
        flex-shrink: 0;
    }
    
    .user-avatar-large-img {
        width: 48px !important;
        height: 48px !important;
        min-width: 48px;
        min-height: 48px;
        flex-shrink: 0;
    }
    
    /* Dashboard avatar sizes */
    .dashboard-user-avatar {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px;
        min-height: 40px;
        flex-shrink: 0;
    }
    
    .dashboard-user-avatar-mobile {
        width: 35px !important;
        height: 35px !important;
        min-width: 35px;
        min-height: 35px;
        flex-shrink: 0;
    }
    
    .report-user-avatar {
        width: 32px !important;
        height: 32px !important;
        min-width: 32px;
        min-height: 32px;
        flex-shrink: 0;
    }
    
    /* Ensure perfect centering */
    .user-avatar {
        flex-shrink: 0;
    }
    
    /* Active state for selected user */
    .list-group-item.active .user-avatar img {
        border-color: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    /* Comprehensive Responsive Design System */
    
    /* Extra Small Devices (phones, 576px and down) */
    @media (max-width: 575.98px) {
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
        
        /* Mobile user menu improvements */
        .user-menu-trigger {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .user-info {
            margin-top: 0.25rem;
        }
        
        .user-name {
            font-size: 0.875rem;
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
        
        /* Avatar responsiveness */
        .user-avatar-img {
            width: 28px !important;
            height: 28px !important;
        }
        
        .user-avatar-large-img {
            width: 40px !important;
            height: 40px !important;
        }
        
        .dashboard-user-avatar {
            width: 32px !important;
            height: 32px !important;
        }
        
        .dashboard-user-avatar-mobile {
            width: 30px !important;
            height: 30px !important;
        }
        
        .report-user-avatar {
            width: 28px !important;
            height: 28px !important;
        }
        
        /* Mobile-specific dashboard adjustments */
        .dashboard-sidebar {
            margin-bottom: 1rem;
        }
        
        .stat-card {
            margin-bottom: 1rem;
        }
        
        /* Mobile table improvements */
        .table-responsive {
            font-size: 12px;
        }
        
        .table-responsive .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 11px;
        }
        
        /* Mobile-specific dashboard improvements */
        .dashboard-sidebar {
            order: 2;
        }
        
        .dashboard-main {
            order: 1;
        }
        
        /* Mobile card improvements */
        .card {
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        /* Mobile button improvements */
        .btn-animated {
            transition: all 0.2s ease;
        }
        
        .btn-animated:active {
            transform: scale(0.95);
        }
    }
    
    /* Small Devices (landscape phones, 576px and up) */
    @media (min-width: 576px) and (max-width: 767.98px) {
        .container {
            max-width: 540px;
        }
        
        /* Improved touch targets for tablets */
        .btn {
            min-height: 40px;
            padding: 0.5rem 0.75rem;
        }
        
        /* Better form controls for tablets */
        .form-control,
        .form-select {
            padding: 0.5rem 0.75rem;
        }
        
        /* Improved card spacing */
        .card-body {
            padding: 1.25rem 1rem !important;
        }
        
        /* Tablet-specific avatar sizes */
        .user-avatar-img {
            width: 30px !important;
            height: 30px !important;
        }
        
        .user-avatar-large-img {
            width: 44px !important;
            height: 44px !important;
        }
        
        .dashboard-user-avatar {
            width: 36px !important;
            height: 36px !important;
        }
        
        .dashboard-user-avatar-mobile {
            width: 32px !important;
            height: 32px !important;
        }
        
        .report-user-avatar {
            width: 30px !important;
            height: 30px !important;
        }
    }
    
    /* Medium Devices (tablets, 768px and up) */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .container {
            max-width: 720px;
        }
        
        /* Tablet avatar sizes */
        .user-avatar-img {
            width: 32px !important;
            height: 32px !important;
        }
        
        .user-avatar-large-img {
            width: 48px !important;
            height: 48px !important;
        }
        
        .dashboard-user-avatar {
            width: 38px !important;
            height: 38px !important;
        }
        
        .dashboard-user-avatar-mobile {
            width: 34px !important;
            height: 34px !important;
        }
        
        .report-user-avatar {
            width: 32px !important;
            height: 32px !important;
        }
        
        /* Tablet-specific dashboard layout */
        .dashboard-sidebar {
            margin-bottom: 1.5rem;
        }
    }
    
    /* Large Devices (desktops, 992px and up) */
    @media (min-width: 992px) and (max-width: 1199.98px) {
        .container {
            max-width: 960px;
        }
        
        /* Desktop avatar sizes */
        .user-avatar-img {
            width: 32px !important;
            height: 32px !important;
        }
        
        .user-avatar-large-img {
            width: 48px !important;
            height: 48px !important;
        }
        
        .dashboard-user-avatar {
            width: 40px !important;
            height: 40px !important;
        }
        
        .dashboard-user-avatar-mobile {
            width: 35px !important;
            height: 35px !important;
        }
        
        .report-user-avatar {
            width: 32px !important;
            height: 32px !important;
        }
    }
    
    /* Extra Large Devices (large desktops, 1200px and up) */
    @media (min-width: 1200px) {
        .container {
            max-width: 1140px;
        }
        
        /* Large desktop avatar sizes */
        .user-avatar-img {
            width: 32px !important;
            height: 32px !important;
        }
        
        .user-avatar-large-img {
            width: 48px !important;
            height: 48px !important;
        }
        
        .dashboard-user-avatar {
            width: 40px !important;
            height: 40px !important;
        }
        
        .dashboard-user-avatar-mobile {
            width: 35px !important;
            height: 35px !important;
        }
        
        .report-user-avatar {
            width: 32px !important;
            height: 32px !important;
        }
    }
    
    /* Ultra Wide Screens (1400px and up) */
    @media (min-width: 1400px) {
        .container {
            max-width: 1320px;
        }
    }
    
    /* High DPI Displays */
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
        .user-avatar img {
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
        }
    }
    
    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
        .user-avatar img {
            border-color: #495057;
        }
        
        .user-avatar img:hover {
            border-color: #0d6efd;
        }
    }
    
    /* Reduced Motion Support */
    @media (prefers-reduced-motion: reduce) {
        .user-avatar img,
        .btn,
        .animate-fade-in,
        .animate-slide-up {
            transition: none !important;
            animation: none !important;
        }
    }
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
    .notification-alert {
        border-radius: 1rem;
        border: none;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        margin-bottom: 1rem;
        animation: notificationSlideIn 0.5s ease;
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
    }

    @keyframes notificationSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .notification-alert.alert-success {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(40, 167, 69, 0.05) 100%);
        border-left: 4px solid #28a745;
    }

    .notification-alert.alert-danger {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%);
        border-left: 4px solid #dc3545;
    }

    .notification-alert.alert-warning {
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.05) 100%);
        border-left: 4px solid #ffc107;
    }

    .notification-alert.alert-info {
        background: linear-gradient(135deg, rgba(23, 162, 184, 0.1) 0%, rgba(23, 162, 184, 0.05) 100%);
        border-left: 4px solid #17a2b8;
    }

    .notification-alert .btn-close {
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }

    .notification-alert .btn-close:hover {
        opacity: 1;
    }

    .notification-alert strong {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    /* Floating notification container */
    #notification-container {
        pointer-events: none;
    }

    #notification-container .alert {
        pointer-events: auto;
        margin-bottom: 0.5rem;
        min-width: 300px;
        max-width: 400px;
    }

    /* Mobile responsive notifications */
    @media (max-width: 768px) {
        #notification-container {
            padding: 0.5rem !important;
        }
        
        #notification-container .alert {
            min-width: 250px;
            max-width: 300px;
            font-size: 0.9rem;
        }
        
        .notification-alert {
            margin-bottom: 0.5rem;
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
    
    /* User Avatar and Dropdown Styles */
    .user-menu-trigger {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
        color: #495057;
    }
    
    .user-menu-trigger:hover {
        background-color: #f8f9fa;
        color: #0d6efd;
    }
    
    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .user-avatar:hover {
        border-color: #0d6efd;
        transform: scale(1.05);
    }
    
    .user-avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .user-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
    
    .user-name {
        font-weight: 500;
        color: #495057;
        line-height: 1.2;
    }
    
    .dropdown-arrow {
        font-size: 0.75rem;
        color: #6c757d;
        transition: transform 0.3s ease;
    }
    
    .user-menu-trigger[aria-expanded="true"] .dropdown-arrow {
        transform: rotate(180deg);
    }
    
    /* User Dropdown Menu */
    .user-dropdown {
        min-width: 280px;
        border-radius: 1rem;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        padding: 0;
        margin-top: 0.5rem;
        animation: dropdownSlideDown 0.3s ease;
    }
    
    @keyframes dropdownSlideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .user-dropdown-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1.5rem;
        border-radius: 1rem 1rem 0 0;
        border-bottom: 1px solid #dee2e6;
    }
    
    .user-avatar-large {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .user-avatar-large-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .user-details {
        flex: 1;
    }
    
    .user-name-large {
        font-weight: 600;
        font-size: 1.1rem;
        color: #212529;
        margin-bottom: 0.25rem;
    }
    
    .user-email {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    
    .user-role {
        margin-top: 0.25rem;
    }
    
    .user-dropdown .dropdown-item {
        padding: 0.75rem 1.5rem;
        color: #495057;
        transition: all 0.2s ease;
        border-radius: 0;
    }
    
    .user-dropdown .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #0d6efd;
        transform: translateX(5px);
    }
    
    .user-dropdown .dropdown-item:first-of-type {
        border-radius: 0;
    }
    
    .user-dropdown .dropdown-item:last-of-type {
        border-radius: 0 0 1rem 1rem;
    }
    
    .user-dropdown .dropdown-divider {
        margin: 0;
        border-color: #dee2e6;
    }
    
    /* Mobile Responsive for User Menu */
    @media (max-width: 768px) {
        .user-menu-trigger {
            padding: 0.5rem;
        }
        
        .user-avatar {
            width: 28px;
            height: 28px;
        }
        
        .user-dropdown {
            min-width: 250px;
            margin-top: 0.25rem;
        }
        
        .user-dropdown-header {
            padding: 1rem;
        }
        
        .user-avatar-large {
            width: 50px;
            height: 50px;
        }
        
        .user-name-large {
            font-size: 1rem;
        }
        
        .user-email {
            font-size: 0.8rem;
        }
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
                                <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center user-menu-trigger" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <!-- User Avatar -->
                                    <div class="user-avatar me-2">
                                        <img src="{{ auth()->user()->avatar_url }}" 
                                             alt="{{ auth()->user()->getAvatarDisplayName() }}" 
                                             class="user-avatar-img rounded-circle"
                                             loading="lazy"
                                             onerror="this.src='{{ asset('images/default-avatar.png') }}'"
                                             onload="this.style.opacity='1'" style="opacity: 0; transition: opacity 0.3s ease;">
                                    </div>
                                    <!-- User Name -->
                                    <div class="user-info">
                                        <span class="user-name d-none d-lg-inline">{{ Auth::user()->name }}</span>
                                        <span class="user-name d-inline d-lg-none">{{ Str::limit(Auth::user()->name, 10) }}</span>
                                        @if(auth()->user()->role === 'admin')
                                            <span class="badge bg-danger ms-1">管理者</span>
                                        @endif
                                    </div>

                                </a>
                                <div class="dropdown-menu dropdown-menu-end user-dropdown" aria-labelledby="navbarDropdown">
                                    <!-- User Header -->
                                    <div class="dropdown-header user-dropdown-header">
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar-large me-3">
                                                <img src="{{ auth()->user()->avatar_url }}" 
                                                     alt="{{ auth()->user()->getAvatarDisplayName() }}" 
                                                     class="user-avatar-large-img rounded-circle"
                                                     loading="lazy"
                                                     onerror="this.src='{{ asset('images/default-avatar.png') }}'"
                                                     onload="this.style.opacity='1'" style="opacity: 0; transition: opacity 0.3s ease;">
                                            </div>
                                            <div class="user-details">
                                                <div class="user-name-large">{{ Auth::user()->name }}</div>
                                                <div class="user-email">{{ Auth::user()->email }}</div>
                                                @if(auth()->user()->role === 'admin')
                                                    <div class="user-role">
                                                        <span class="badge bg-danger">管理者</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <!-- Menu Items -->
                                    <a class="dropdown-item" href="{{ route('owner.edit') }}">
                                        <i class="fas fa-user-edit me-2"></i>プロフィール編集
                                    </a>
                                    @if(auth()->user()->role !== 'admin')
                                        <a class="dropdown-item" href="{{ route('showForm') }}">
                                            <i class="fas fa-plus me-2"></i>新規レポート作成
                                        </a>
                                        <a class="dropdown-item" href="{{ route('myReports') }}">
                                            <i class="fas fa-list me-2"></i>マイレポート
                                        </a>
                                    @endif
                                    @if(auth()->user()->role === 'admin')
                                        <a class="dropdown-item" href="{{ route('dashboard') }}">
                                            <i class="fas fa-tachometer-alt me-2"></i>管理ダッシュボード
                                        </a>
                                    @endif
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
            <!-- Enhanced Notification System -->
            <div id="notification-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                <!-- Notifications will be dynamically inserted here -->
            </div>

            @if(session('success'))
                <div class="container">
                    <div class="alert alert-success alert-dismissible fade show notification-alert" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-2 fs-4"></i>
                            <div>
                                <strong class="d-block">成功</strong>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="閉じる"></button>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="container">
                    <div class="alert alert-danger alert-dismissible fade show notification-alert" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-2 fs-4"></i>
                            <div>
                                <strong class="d-block">エラー</strong>
                                <span>{{ session('error') }}</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="閉じる"></button>
                    </div>
                </div>
            @endif
            @if(session('warning'))
                <div class="container">
                    <div class="alert alert-warning alert-dismissible fade show notification-alert" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2 fs-4"></i>
                            <div>
                                <strong class="d-block">警告</strong>
                                <span>{{ session('warning') }}</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="閉じる"></button>
                    </div>
                </div>
            @endif
            @if(session('info'))
                <div class="container">
                    <div class="alert alert-info alert-dismissible fade show notification-alert" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2 fs-4"></i>
                            <div>
                                <strong class="d-block">情報</strong>
                                <span>{{ session('info') }}</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="閉じる"></button>
                    </div>
                </div>
            @endif
            <div class="site-content">
                @yield('content')
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script>
    // User dropdown menu enhancements
    document.addEventListener('DOMContentLoaded', function() {
        const userMenuTrigger = document.querySelector('.user-menu-trigger');
        const userDropdown = document.querySelector('.user-dropdown');
        const dropdownArrow = document.querySelector('.dropdown-arrow');
        
        if (userMenuTrigger && userDropdown) {
            // Enhanced dropdown functionality
            userMenuTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Toggle dropdown
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !isExpanded);
                
                // Animate arrow
                if (dropdownArrow) {
                    dropdownArrow.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
                }
                
                // Show/hide dropdown with animation
                if (!isExpanded) {
                    userDropdown.style.display = 'block';
                    userDropdown.style.opacity = '0';
                    userDropdown.style.transform = 'translateY(-10px)';
                    
                    setTimeout(() => {
                        userDropdown.style.opacity = '1';
                        userDropdown.style.transform = 'translateY(0)';
                    }, 10);
                } else {
                    userDropdown.style.opacity = '0';
                    userDropdown.style.transform = 'translateY(-10px)';
                    
                    setTimeout(() => {
                        userDropdown.style.display = 'none';
                    }, 300);
                }
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userMenuTrigger.contains(e.target) && !userDropdown.contains(e.target)) {
                    userMenuTrigger.setAttribute('aria-expanded', 'false');
                    if (dropdownArrow) {
                        dropdownArrow.style.transform = 'rotate(0deg)';
                    }
                    userDropdown.style.opacity = '0';
                    userDropdown.style.transform = 'translateY(-10px)';
                    
                    setTimeout(() => {
                        userDropdown.style.display = 'none';
                    }, 300);
                }
            });
            
            // Enhanced hover effects for dropdown items
            const dropdownItems = userDropdown.querySelectorAll('.dropdown-item');
            dropdownItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(5px)';
                });
                
                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });
        }
        
        // Avatar image error handling
        const avatarImages = document.querySelectorAll('.user-avatar-img, .user-avatar-large-img');
        avatarImages.forEach(img => {
            img.addEventListener('error', function() {
                this.src = '{{ asset("images/default-avatar.png") }}';
            });
        });

        // Enhanced notification system
        window.showNotification = function(message, type = 'success', title = null) {
            const container = document.getElementById('notification-container');
            const notification = document.createElement('div');
            
            const iconMap = {
                'success': 'fas fa-check-circle',
                'error': 'fas fa-exclamation-circle',
                'warning': 'fas fa-exclamation-triangle',
                'info': 'fas fa-info-circle'
            };
            
            const titleMap = {
                'success': '成功',
                'error': 'エラー',
                'warning': '警告',
                'info': '情報'
            };
            
            notification.className = `alert alert-${type} alert-dismissible fade show notification-alert`;
            notification.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="${iconMap[type]} me-2 fs-4"></i>
                    <div>
                        <strong class="d-block">${title || titleMap[type]}</strong>
                        <span>${message}</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            container.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateY(-20px) scale(0.95)';
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }
            }, 5000);
        };

        // Show notifications from server
        @if(session('success'))
            showNotification('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showNotification('{{ session('error') }}', 'error');
        @endif

        @if(session('warning'))
            showNotification('{{ session('warning') }}', 'warning');
        @endif

        @if(session('info'))
            showNotification('{{ session('info') }}', 'info');
        @endif

        // User activity tracking
        let lastActivity = Date.now();
        const activityTimeout = 30 * 60 * 1000; // 30 minutes

        function updateActivity() {
            lastActivity = Date.now();
        }

        function checkInactivity() {
            const now = Date.now();
            if (now - lastActivity > activityTimeout) {
                showNotification('セッションがタイムアウトしました。再度ログインしてください。', 'warning', 'セッションタイムアウト');
                // Redirect to login after showing notification
                setTimeout(() => {
                    window.location.href = '{{ route("login") }}';
                }, 3000);
            }
        }

        // Track user activity
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
            document.addEventListener(event, updateActivity, true);
        });

        // Check inactivity every minute
        setInterval(checkInactivity, 60000);
    });
    </script>
    @stack('scripts')
</body>
</html>
