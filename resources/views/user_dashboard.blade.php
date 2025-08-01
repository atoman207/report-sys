@extends('layouts.app')

@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
<!-- Notification System -->
<div id="notification-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <!-- Notifications will be dynamically inserted here -->
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- <div class="mb-3">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-animated">
                    <i class="fas fa-arrow-left me-2"></i>ダッシュボードに戻る
                </a>
            </div> -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="animate-fade-in">
                    <h2 class="mb-1 fs-4 fw-bold text-primary">マイダッシュボード</h2>
                    <div class="text-muted small">{{ auth()->user()->name }}さん、お疲れ様です！</div>
                </div>
                <div class="text-end">
                    <a href="{{ route('showForm') }}" class="btn btn-primary btn-animated">
                        <i class="fas fa-plus me-2"></i>新規レポート作成
                    </a>
                </div>
            </div>
            <!-- 統計カード -->
            <div class="row mb-4 g-3">
                <div class="col-4">
                    <div class="card stat-card animate-slide-up" data-delay="0">
                        <div class="card-body text-center p-3">
                            <div class="stat-icon mb-2">
                                <i class="fas fa-calendar-day text-primary"></i>
                            </div>
                            <div class="fw-bold text-muted">今日のレポート</div>
                            <div class="fs-3 fw-bold text-primary">{{ $todayReports->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card stat-card animate-slide-up" data-delay="0.1">
                        <div class="card-body text-center p-3">
                            <div class="stat-icon mb-2">
                                <i class="fas fa-calendar-alt text-success"></i>
                            </div>
                            <div class="fw-bold text-muted">今月のレポート</div>
                            <div class="fs-3 fw-bold text-success">{{ $monthReports->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card stat-card animate-slide-up" data-delay="0.2">
                        <div class="card-body text-center p-3">
                            <div class="stat-icon mb-2">
                                <i class="fas fa-chart-bar text-info"></i>
                            </div>
                            <div class="fw-bold text-muted">総レポート数</div>
                            <div class="fs-3 fw-bold text-info">{{ $totalReports->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- モバイル用: レポート一覧をボタンで表示 -->
            <div class="d-block d-md-none mb-3">
                @foreach($recentReports as $index => $report)
                    <div class="report-item animate-slide-up" data-delay="{{ $index * 0.1 }}">
                        <button class="btn btn-outline-primary w-100 text-start mb-2 report-toggle-btn" data-report-id="{{ $report->id }}">
                            <i class="fas fa-file-alt me-2"></i>{{ $report->created_at->format('m/d') }} のレポート
                        </button>
                        <div class="report-details card mb-2" id="report-details-{{ $report->id }}" style="display:none;">
                            <div class="card-body">
                                <div class="report-detail-item"><strong>会社名:</strong> {{ $report->company }}</div>
                                <div class="report-detail-item"><strong>工事分類:</strong> {{ $report->work_type }}</div>
                                <div class="report-detail-item"><strong>作業分類:</strong> {{ $report->task_type }}</div>
                                <div class="report-detail-item"><strong>訪問ステータス:</strong> {{ $report->visit_status }}</div>
                                <div class="report-detail-item"><strong>作業内容:</strong> {{ $report->work_detail }}</div>
                                <div class="report-detail-item">
                                    <strong>画像:</strong>
                                    @if($report->images && is_array($report->images))
                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                            @foreach($report->images as $img)
                                                <img src="{{ Storage::url($img) }}" class="img-thumbnail report-img-thumb" style="width:70px;height:70px;object-fit:cover;cursor:zoom-in;" data-fullsrc="{{ Storage::url($img) }}">
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">なし</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- デスクトップ/タブレット用: 既存のテーブル -->
            <div class="d-none d-md-block">
                <!-- 今日のレポート -->
                <div class="card animate-slide-up" data-delay="0.3">
                    <div class="card-header">
                        <i class="fas fa-calendar-day me-2"></i>今日のレポート
                    </div>
                    <div class="card-body p-0">
                        @if($todayReports->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>会社名</th>
                                            <th>工事分類</th>
                                            <th>作業分類</th>
                                            <th>訪問ステータス</th>
                                            <th>画像</th>
                                            <th>作成時間</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($todayReports as $index => $report)
                                            <tr class="animate-fade-in" data-delay="{{ $index * 0.05 }}">
                                                <td><span class="badge bg-secondary">#{{ $report->id }}</span></td>
                                                <td>{{ $report->company }}</td>
                                                <td>{{ $report->work_type }}</td>
                                                <td>{{ $report->task_type }}</td>
                                                <td>{{ $report->visit_status }}</td>
                                                <td>
                                                    @if($report->images && is_array($report->images))
                                                        <div class="d-flex flex-wrap gap-1">
                                                            @foreach(array_slice($report->images, 0, 2) as $img)
                                                                <a href="{{ Storage::url($img) }}" target="_blank" class="btn btn-sm btn-outline-primary">画像</a>
                                                            @endforeach
                                                            @if(count($report->images) > 2)
                                                                <span class="badge bg-secondary">+{{ count($report->images) - 2 }}</span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-muted">なし</span>
                                                    @endif
                                                </td>
                                                <td><small>{{ $report->created_at->format('H:i') }}</small></td>
                                                <td>
                                                    <a href="{{ route('editReport', $report->id) }}" class="btn btn-sm btn-outline-primary btn-animated">編集</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>今日のレポートはありません。</p>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- 最近のレポート（過去7日間） -->
                <div class="card animate-slide-up" data-delay="0.4">
                    <div class="card-header">
                        <i class="fas fa-history me-2"></i>最近のレポート（過去7日間）
                    </div>
                    <div class="card-body p-0">
                        @if($recentReports->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>日付</th>
                                            <th>会社名</th>
                                            <th>工事分類</th>
                                            <th>訪問ステータス</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentReports as $index => $report)
                                            <tr class="animate-fade-in" data-delay="{{ $index * 0.05 }}">
                                                <td>{{ $report->created_at->format('m/d') }}</td>
                                                <td>{{ $report->company }}</td>
                                                <td>{{ $report->work_type }}</td>
                                                <td>{{ $report->visit_status }}</td>
                                                <td>
                                                    <a href="{{ route('editReport', $report->id) }}" class="btn btn-sm btn-outline-primary btn-animated">編集</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>最近のレポートはありません。</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 画像プレビューモーダル -->
<div id="image-modal" class="modal fade" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">画像プレビュー</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modal-image" src="" alt="Original" style="max-width:100%; max-height:70vh; border-radius:0.7rem; box-shadow:0 0 16px #aaa;">
      </div>
    </div>
  </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation system
    function animateOnScroll() {
        const elements = document.querySelectorAll('.animate-slide-up, .animate-fade-in');
        elements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < window.innerHeight - elementVisible) {
                const delay = element.getAttribute('data-delay') || 0;
                setTimeout(() => {
                    element.classList.add('animate-in');
                }, delay * 1000);
            }
        });
    }

    // Initial animation
    animateOnScroll();
    window.addEventListener('scroll', animateOnScroll);

    // Toggle report details on button click
    document.querySelectorAll('.report-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-report-id');
            const details = document.getElementById('report-details-' + id);
            if (details.style.display === 'none') {
                document.querySelectorAll('.report-details').forEach(d => d.style.display = 'none');
                details.style.display = 'block';
                details.classList.add('animate-slide-down');
            } else {
                details.style.display = 'none';
            }
        });
    });

    // Image modal preview
    document.querySelectorAll('.report-img-thumb').forEach(img => {
        img.addEventListener('dblclick', function() {
            const modalImg = document.getElementById('modal-image');
            modalImg.src = this.getAttribute('data-fullsrc');
            const modal = new bootstrap.Modal(document.getElementById('image-modal'));
            modal.show();
        });
    });

    // Clear modal image src when modal is closed
    const imageModal = document.getElementById('image-modal');
    if (imageModal && !imageModal.hasAttribute('data-listener')) {
        imageModal.setAttribute('data-listener', 'true');
        imageModal.addEventListener('hidden.bs.modal', function() {
            document.getElementById('modal-image').src = '';
        });
    }

    // Notification system
    window.showNotification = function(message, type = 'success') {
        const container = document.getElementById('notification-container');
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show notification-item`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        container.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    };

    // Check for success message from server
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif

    @if(session('error'))
        showNotification('{{ session('error') }}', 'danger');
    @endif
});
</script>
@endpush
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
/* Modern Card Styling */
.card {
    border-radius: 1rem;
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.stat-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-left: 4px solid #007bff;
}

.stat-icon {
    font-size: 2rem;
    opacity: 0.8;
}

/* Animation Classes */
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

.animate-slide-down {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Button Animations */
.btn-animated {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-animated:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

.btn-animated:active {
    transform: translateY(0);
}

/* Table Styling */
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
    transform: scale(1.01);
    transition: all 0.2s ease;
}

/* Notification Styling */
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

/* Report Detail Items */
.report-detail-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.report-detail-item:last-child {
    border-bottom: none;
}

/* Badge Styling */
.badge {
    font-size: 0.75em;
    border-radius: 0.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .stat-card .card-body {
        padding: 1rem 0.5rem;
    }
    
    .stat-icon {
        font-size: 1.5rem;
    }
    
    .fs-3 {
        font-size: 1.5rem !important;
    }
}

/* Loading Animation */
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
</style>
@endpush 