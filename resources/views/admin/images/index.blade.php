@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1 fs-4 fw-bold text-primary">
                        <i class="fas fa-images me-2"></i>画像管理ダッシュボード
                    </h2>
                    <div class="text-muted small">システム内の全画像の管理・プレビュー・ダウンロード</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.images.downloadAll') }}" class="btn btn-success btn-animated">
                        <i class="fas fa-download me-1"></i>全画像ダウンロード
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-animated">
                        <i class="fas fa-arrow-left me-1"></i>ダッシュボードに戻る
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4 g-3">
                <div class="col-6 col-lg-3">
                    <div class="card stat-card animate-slide-up" data-delay="0.1">
                        <div class="card-body text-center p-3">
                            <div class="stat-icon mb-2">
                                <i class="fas fa-file-image text-primary"></i>
                            </div>
                            <div class="fw-bold text-muted">画像付きレポート</div>
                            <div class="fs-3 fw-bold text-primary">{{ $imageStats['total_reports_with_images'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card stat-card animate-slide-up" data-delay="0.2">
                        <div class="card-body text-center p-3">
                            <div class="stat-icon mb-2">
                                <i class="fas fa-images text-success"></i>
                            </div>
                            <div class="fw-bold text-muted">総画像数</div>
                            <div class="fs-3 fw-bold text-success">{{ $imageStats['total_images'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card stat-card animate-slide-up" data-delay="0.3">
                        <div class="card-body text-center p-3">
                            <div class="stat-icon mb-2">
                                <i class="fas fa-hdd text-info"></i>
                            </div>
                            <div class="fw-bold text-muted">総ファイルサイズ</div>
                            <div class="fs-3 fw-bold text-info">{{ $imageStats['formatted_total_size'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card stat-card animate-slide-up" data-delay="0.4">
                        <div class="card-body text-center p-3">
                            <div class="stat-icon mb-2">
                                <i class="fas fa-signature text-warning"></i>
                            </div>
                            <div class="fw-bold text-muted">署名付きレポート</div>
                            <div class="fs-3 fw-bold text-warning">{{ $imageStats['reports_with_signatures'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reports with Images -->
            <div class="card animate-slide-up" data-delay="0.5">
                <div class="card-header">
                    <i class="fas fa-list me-2"></i>画像付きレポート一覧
                </div>
                <div class="card-body p-0">
                    @if($reports->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-images fa-3x mb-3"></i>
                            <p class="fs-5">画像付きのレポートがありません。</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>レポートID</th>
                                        <th>送信者</th>
                                        <th>会社名</th>
                                        <th>画像</th>
                                        <th>署名</th>
                                        <th>作成日時</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $report)
                                        <tr class="animate-fade-in" data-delay="{{ $loop->index * 0.03 }}">
                                            <td>
                                                <span class="badge bg-secondary">#{{ $report->id }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar me-2">
                                                        <img src="{{ $report->user->avatar_url ?? asset('images/default-avatar.png') }}" 
                                                             alt="{{ $report->user ? $report->user->getAvatarDisplayName() : 'Unknown User' }}" 
                                                             class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover;"
                                                             onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $report->user->name ?? '不明' }}</div>
                                                        <div class="small text-muted">{{ $report->user->email ?? '' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $report->company }}</td>
                                            <td class="images-column">
                                                @if($report->hasImages())
                                                    @php $normalizedImages = $report->normalized_images; @endphp
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach(array_slice($normalizedImages, 0, 3) as $index => $imagePath)
                                                            @php
                                                                $imageNumber = $index + 1;
                                                                $filename = basename($imagePath);
                                                                $fullPath = Storage::disk('public')->path($imagePath);
                                                                $exists = Storage::disk('public')->exists($imagePath);
                                                            @endphp
                                                            @if($exists)
                                                                <div class="image-thumbnail-container">
                                                                    <a href="{{ route('admin.images.preview', ['reportId' => $report->id, 'imagePath' => urlencode($imagePath)]) }}" 
                                                                       class="d-block">
                                                                        <img src="{{ route('admin.images.file', ['path' => $imagePath]) }}" 
                                                                             alt="画像{{ $imageNumber }}: {{ $filename }}"
                                                                             class="image-thumbnail">
                                                                        <div class="image-overlay">
                                                                            <small class="text-white">{{ $filename }}</small>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                            @else
                                                                <div class="image-placeholder">
                                                                    <i class="fas fa-image text-muted"></i>
                                                                    <small class="text-muted d-block">ファイルなし</small>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                        @if(count($normalizedImages) > 3)
                                                            <div class="more-images-badge">
                                                                <span class="badge bg-secondary">+{{ count($normalizedImages) - 3 }}</span>
                                                            </div>
                                                        @endif
                                                        <div class="mt-1">
                                                            <small class="text-muted">{{ count($normalizedImages) }}枚</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted"><i class="fas fa-times me-1"></i>なし</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($report->normalized_signature)
                                                    @php
                                                        $signatureExists = Storage::disk('public')->exists($report->normalized_signature);
                                                    @endphp
                                                    @if($signatureExists)
                                                        <div class="signature-thumbnail-container">
                                                            <a href="{{ route('admin.images.preview', ['reportId' => $report->id, 'imagePath' => urlencode($report->normalized_signature)]) }}" 
                                                               class="d-block">
                                                                <img src="{{ route('admin.images.file', ['path' => $report->normalized_signature]) }}" 
                                                                     alt="署名: {{ basename($report->normalized_signature) }}"
                                                                     class="signature-thumbnail">
                                                                <div class="signature-overlay">
                                                                    <small class="text-white">署名</small>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div class="signature-placeholder">
                                                            <i class="fas fa-signature text-muted"></i>
                                                            <small class="text-muted d-block">ファイルなし</small>
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="text-muted">なし</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $report->created_at->format('Y-m-d H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @if($report->hasImages() || $report->signature)
                                                        <a href="{{ route('admin.images.downloadReport', $report->id) }}" 
                                                           class="btn btn-outline-success btn-animated">
                                                            <i class="fas fa-download me-1"></i>ダウンロード
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('editReport', $report->id) }}" 
                                                       class="btn btn-outline-primary btn-animated">
                                                        <i class="fas fa-edit me-1"></i>編集
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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
    
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
    }
}

/* Image container layout improvements */
.images-column {
    position: relative;
    min-height: 100px;
}

.images-column .d-flex {
    position: relative;
    flex-wrap: wrap;
    gap: 4px;
}

/* Responsive adjustments for thumbnails */
@media (max-width: 768px) {
    .image-thumbnail,
    .signature-thumbnail {
        width: 80px;
        height: 60px;
    }
    
    .image-placeholder,
    .signature-placeholder {
        width: 80px;
        height: 60px;
    }
    
    .more-images-badge {
        font-size: 0.75rem;
        padding: 2px 6px;
    }
}
</style>
@endpush 