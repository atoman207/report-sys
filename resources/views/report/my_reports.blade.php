@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="animate-fade-in d-flex align-items-center">
                    <div class="user-avatar me-3">
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->getAvatarDisplayName() }}" 
                             class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.15);"
                             onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                    </div>
                    <div>
                        <h2 class="mb-1 fs-4 fw-bold text-primary">
                            <i class="fas fa-list me-2"></i>マイレポート
                        </h2>
                        <div class="text-muted small">
                            あなたが作成したレポート一覧
                        </div>
                        <div class="text-muted small">
                            <i class="fas fa-user me-1"></i>{{ auth()->user()->name }}さんのレポート
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <a href="{{ route('showForm') }}" class="btn btn-primary btn-animated">
                        <i class="fas fa-plus me-2"></i>新規レポート作成
                    </a>
                </div>
            </div>

            <!-- 統計カード -->
            <div class="row mb-4 g-3">
                <div class="col-6 col-md-3">
                    <div class="card stat-card animate-slide-up" data-delay="0.1">
                        <div class="card-body text-center p-3">
                            <div class="stat-icon mb-2">
                                <i class="fas fa-chart-bar text-primary"></i>
                            </div>
                            <div class="fw-bold text-muted">総レポート数</div>
                            <div class="fs-3 fw-bold text-primary">{{ $reports->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stat-card animate-slide-up" data-delay="0.2">
                        <div class="card-body text-center p-3">
                            <div class="stat-icon mb-2">
                                <i class="fas fa-calendar-day text-success"></i>
                            </div>
                            <div class="fw-bold text-muted">今日のレポート</div>
                            <div class="fs-3 fw-bold text-success">{{ $reports->filter(fn($r)=>$r->created_at->isToday())->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stat-card animate-slide-up" data-delay="0.3">
                        <div class="card-body text-center p-3">
                            <div class="stat-icon mb-2">
                                <i class="fas fa-calendar-alt text-info"></i>
                            </div>
                            <div class="fw-bold text-muted">今月のレポート</div>
                            <div class="fs-3 fw-bold text-info">{{ $reports->filter(fn($r)=>$r->created_at->isCurrentMonth())->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stat-card animate-slide-up" data-delay="0.4">
                        <div class="card-body text-center p-3">
                            <div class="stat-icon mb-2">
                                <i class="fas fa-image text-warning"></i>
                            </div>
                            <div class="fw-bold text-muted">画像付き</div>
                            <div class="fs-3 fw-bold text-warning">{{ $reports->filter(fn($r)=>!empty($r->images))->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- レポート一覧 -->
            <div class="card animate-slide-up" data-delay="0.5">
                <div class="card-header">
                    <i class="fas fa-list me-2"></i>レポート一覧
                </div>
                <div class="card-body p-0">
                    @if($reports->count() > 0)
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
                                        <th>署名</th>
                                        <th>作成日時</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports->sortByDesc('created_at') as $index => $report)
                                        <tr class="animate-fade-in" data-delay="{{ $index * 0.03 }}">
                                            <td><span class="badge bg-secondary">#{{ $report->id }}</span></td>
                                            <td>{{ $report->company }}</td>
                                            <td>{{ $report->work_type }}</td>
                                            <td>{{ $report->task_type }}</td>
                                            <td>
                                                <span class="badge bg-{{ $report->visit_status === '対応済み' ? 'success' : ($report->visit_status === '対応中' ? 'warning' : 'secondary') }}">
                                                    {{ $report->visit_status }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($report->hasImages())
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-image me-1"></i>{{ $report->image_count }}枚
                                                    </span>
                                                @else
                                                    <span class="text-muted"><i class="fas fa-times me-1"></i>なし</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($report->signature)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-signature me-1"></i>あり
                                                    </span>
                                                @else
                                                    <span class="text-muted">なし</span>
                                                @endif
                                            </td>
                                            <td><small>{{ $report->created_at->format('Y-m-d H:i') }}</small></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('editReport', $report->id) }}" class="btn btn-outline-primary btn-animated">
                                                        <i class="fas fa-edit me-1"></i>編集
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">レポートがありません</h5>
                            <p class="text-muted">最初のレポートを作成してみましょう</p>
                            <a href="{{ route('showForm') }}" class="btn btn-primary btn-animated">
                                <i class="fas fa-plus me-2"></i>新規レポート作成
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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

/* Card Styling */
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

/* User Avatar Styling */
.user-avatar {
    position: relative;
    transition: all 0.3s ease;
}

.user-avatar:hover {
    transform: scale(1.05);
}

.user-avatar img {
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.user-avatar:hover img {
    border-color: #007bff;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

/* Avatar loading animation */
.user-avatar img[src*="default-avatar"] {
    opacity: 0.7;
}

/* Mobile avatar adjustments */
@media (max-width: 768px) {
    .user-avatar img {
        width: 40px !important;
        height: 40px !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
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
</script>
@endpush 