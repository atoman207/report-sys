@extends('layouts.app')

@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
<!-- Image Viewing Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">
                    <i class="fas fa-images me-2"></i>レポート画像
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">レポート情報</h6>
                        <p class="mb-1"><strong>レポートID:</strong> <span id="modal-report-id"></span></p>
                        <p class="mb-1"><strong>会社名:</strong> <span id="modal-report-company"></span></p>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-primary btn-sm" id="download-all-btn">
                            <i class="fas fa-download me-1"></i>全画像ダウンロード
                        </button>
                    </div>
                </div>
                <div id="image-carousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators" id="carousel-indicators"></div>
                    <div class="carousel-inner" id="carousel-inner"></div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#image-carousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">前へ</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#image-carousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">次へ</span>
                    </button>
                </div>
                <div class="mt-3">
                    <div class="row" id="image-thumbnails"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
            </div>
        </div>
    </div>
</div>

<!-- Notification System -->
<div id="notification-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <!-- Notifications will be dynamically inserted here -->
</div>

<div class="container-fluid">
    <div class="row">
        <!-- サイドバー（メンバー一覧・管理者管理） -->
        <div class="col-md-3 col-lg-2 mb-3">
            <div class="card mb-3 animate-slide-up" data-delay="0">
                <div class="card-header">
                    <i class="fas fa-users me-2"></i>メンバー一覧
                </div>
                <div class="card-body p-2">
                    <!-- PC/タブレット: テーブル用リスト -->
                    <div class="d-none d-md-block">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ !request('user_id') ? 'active' : '' }} btn-animated">
                                <i class="fas fa-users me-2"></i>全メンバー
                            </a>
                            @foreach($users as $index => $user)
                                <a href="{{ route('dashboard', ['user_id' => $user->id]) }}" class="list-group-item list-group-item-action {{ request('user_id') == $user->id ? 'active' : '' }} btn-animated" data-delay="{{ $index * 0.05 }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>{{ $user->name }}</span>
                                        <div>
                                            <span class="badge bg-secondary">{{ $user->reports_count ?? 0 }}</span>
                                            @if($user->role === 'admin')<span class="badge bg-danger ms-1">管理者</span>@endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <!-- スマホ: ボタン縦並び -->
                    <div class="d-block d-md-none">
                        <button class="btn btn-secondary w-100 mb-2 member-btn btn-animated" data-user="all">
                            <i class="fas fa-users me-2"></i>全メンバー
                        </button>
                        @foreach($users as $index => $user)
                            <button class="btn btn-outline-primary w-100 mb-2 member-btn btn-animated" data-user="{{ $user->id }}" data-delay="{{ $index * 0.05 }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>{{ $user->name }}</span>
                                    <div>
                                        <span class="badge bg-secondary">{{ $user->reports_count ?? 0 }}</span>
                                        @if($user->role === 'admin')<span class="badge bg-danger ms-1">管理者</span>@endif
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <!-- メインコンテンツ -->
        <div class="col-md-9 col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="animate-fade-in">
                    <h2 class="mb-1 fs-4 fw-bold text-primary">
                        @if(request('user_id'))
                            {{ $selectedUser->name }}さんのレポート
                        @else
                            ダッシュボード
                        @endif
                    </h2>
                    <div class="text-muted small">
                        @if(request('user_id'))
                            {{ $selectedUser->email }}
                            @if($selectedUser->role === 'admin')<span class="badge bg-danger ms-2">管理者</span>@endif
                        @else
                            全メンバーのレポート一覧
                        @endif
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary fs-6 animate-pulse">{{ $reports->count() }}件</span>
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
                            <div class="fs-3 fw-bold text-success">{{ $reports->filter(fn($r)=>\Carbon\Carbon::parse($r->created_at)->isToday())->count() }}</div>
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
                            <div class="fs-3 fw-bold text-info">{{ $reports->filter(fn($r)=>\Carbon\Carbon::parse($r->created_at)->isCurrentMonth())->count() }}</div>
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
            
            <!-- 画像統計セクション -->
            @if(isset($imageStats) && $imageStats['total_images'] > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card animate-slide-up" data-delay="0.5">
                        <div class="card-header">
                            <i class="fas fa-images me-2"></i>画像統計
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="fs-4 fw-bold text-primary">{{ $imageStats['total_reports_with_images'] }}</div>
                                        <div class="text-muted small">画像付きレポート</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="fs-4 fw-bold text-success">{{ $imageStats['total_images'] }}</div>
                                        <div class="text-muted small">総画像数</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="fs-4 fw-bold text-info">
                                            @php
                                                $totalSize = $imageStats['total_image_size'];
                                                if ($totalSize < 1024) {
                                                    echo $totalSize . ' B';
                                                } elseif ($totalSize < 1024 * 1024) {
                                                    echo round($totalSize / 1024, 1) . ' KB';
                                                } else {
                                                    echo round($totalSize / (1024 * 1024), 1) . ' MB';
                                                }
                                            @endphp
                                        </div>
                                        <div class="text-muted small">総ファイルサイズ</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="fs-4 fw-bold text-warning">{{ $imageStats['reports_with_signatures'] }}</div>
                                        <div class="text-muted small">署名付きレポート</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <!-- レポート一覧テーブル（PC/タブレット） -->
            <div class="d-none d-md-block">
                <div class="card animate-slide-up" data-delay="0.5">
                    <div class="card-header">
                        <i class="fas fa-list me-2"></i>レポート一覧
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>送信者</th>
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
                                            <td>
                                                <div>{{ $report->user->name ?? '不明' }}</div>
                                                <div class="small text-muted">{{ $report->user->email ?? '' }}</div>
                                            </td>
                                            <td>{{ $report->company }}</td>
                                            <td>{{ $report->work_type }}</td>
                                            <td>{{ $report->task_type }}</td>
                                            <td>{{ $report->visit_status }}</td>
                                            <td>
                                                @if($report->hasImages())
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach(array_slice($report->images, 0, 2) as $img)
                                                            <button type="button" class="btn btn-sm btn-outline-primary btn-animated view-image-btn" 
                                                                    data-images='@json($report->images)' 
                                                                    data-report-id="{{ $report->id }}"
                                                                    data-report-company="{{ $report->company }}">
                                                                <i class="fas fa-image me-1"></i>画像
                                                                <small class="d-block">{{ $report->image_count }}枚</small>
                                                            </button>
                                                        @endforeach
                                                        @if(count($report->images) > 2)
                                                            <span class="badge bg-secondary">+{{ count($report->images) - 2 }}</span>
                                                        @endif
                                                    </div>
                                                    <small class="text-muted">{{ $report->formatted_image_size }}</small>
                                                    <div class="mt-1">
                                                        @php
                                                            $accessibleCount = 0;
                                                            if (isset($report->_image_accessibility)) {
                                                                $accessibleCount = collect($report->_image_accessibility)->filter(fn($img) => $img['exists'])->count();
                                                            }
                                                        @endphp
                                                        @if($accessibleCount == count($report->images))
                                                            <small class="text-success">
                                                                <i class="fas fa-check-circle me-1"></i>{{ count($report->images) }}枚の画像が保存されています
                                                            </small>
                                                        @else
                                                            <small class="text-warning">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>{{ $accessibleCount }}/{{ count($report->images) }}枚の画像がアクセス可能です
                                                            </small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted"><i class="fas fa-times me-1"></i>なし</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($report->signature)
                                                    <a href="{{ Storage::url($report->signature) }}" target="_blank" class="btn btn-sm btn-outline-success btn-animated">署名</a>
                                                @else
                                                    <span class="text-muted">なし</span>
                                                @endif
                                            </td>
                                            <td><small>{{ $report->created_at->format('Y-m-d H:i') }}</small></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('editReport', $report->id) }}" class="btn btn-outline-primary btn-animated">編集</a>
                                                    @if(auth()->user()->role === 'admin')
                                                        <form action="{{ route('deleteReport', $report->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-animated" onclick="return confirm('本当に削除しますか？')">削除</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- スマホ: メンバー名ボタン押下でレポート表示 -->
            <div class="d-block d-md-none mt-3" id="mobile-report-list">
                <!-- JSで動的に挿入 -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
// スマホ用: メンバー名ボタン押下でレポート表示
const allReports = @json($reports->sortByDesc('created_at')->values());
const users = @json($users->keyBy('id'));
const mobileReportList = document.getElementById('mobile-report-list');

function renderMobileReports(userId) {
    let reports = allReports;
    if (userId && userId !== 'all') {
        reports = reports.filter(r => r.user_id == userId);
    }
    let html = '';
    if (reports.length === 0) {
        html = '<div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2"></i><p>レポートがありません。</p></div>';
    } else {
        html += '<div class="list-group">';
        reports.forEach((r, index) => {
            html += `<div class='list-group-item mb-2 animate-slide-up' data-delay='${index * 0.05}'>
                <div class='fw-bold mb-1'>${r.company} <span class='badge bg-secondary'>#${r.id}</span></div>
                <div class='small text-muted mb-1'><i class="fas fa-user me-1"></i>${users[r.user_id]?.name || '不明'} / <i class="fas fa-tools me-1"></i>${r.work_type} / <i class="fas fa-tasks me-1"></i>${r.task_type}</div>
                <div class='mb-1'><i class="fas fa-map-marker-alt me-1"></i>${r.visit_status} / <i class="fas fa-clock me-1"></i>${r.created_at.substring(0,16).replace('T',' ')}</div>
                <div class='d-flex flex-wrap gap-1 mb-1'>
                    ${(r.images && r.images.length) ? `<button type="button" class="btn btn-sm btn-outline-primary btn-animated view-image-btn" data-images='${JSON.stringify(r.images)}' data-report-id="${r.id}" data-report-company="${r.company}"><i class="fas fa-image me-1"></i>画像 (${r.images.length}枚)</button>` : '<span class="text-muted"><i class="fas fa-times me-1"></i>画像なし</span>'}
                    ${r.signature ? `<a href='/storage/${r.signature}' target='_blank' class='btn btn-sm btn-outline-success btn-animated'><i class="fas fa-signature me-1"></i>署名</a>` : '<span class="text-muted"><i class="fas fa-times me-1"></i>署名なし</span>'}
                </div>
                <a href='/report/${r.id}/edit' class='btn btn-sm btn-outline-primary btn-animated'><i class="fas fa-edit me-1"></i>編集</a>
            </div>`;
        });
        html += '</div>';
    }
    mobileReportList.innerHTML = html;
    
    // Trigger animations for new content
    setTimeout(() => {
        const elements = mobileReportList.querySelectorAll('.animate-slide-up');
        elements.forEach(element => {
            const delay = element.getAttribute('data-delay') || 0;
            setTimeout(() => {
                element.classList.add('animate-in');
            }, delay * 1000);
        });
    }, 100);
}

document.querySelectorAll('.member-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.member-btn').forEach(b => b.classList.remove('btn-primary'));
        this.classList.add('btn-primary');
        renderMobileReports(this.dataset.user);
    });
});

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

// 初期表示: 全メンバー
if(window.innerWidth < 768) renderMobileReports('all');

// Image viewing functionality
let currentImages = [];
let currentReportId = '';
let currentReportCompany = '';

// Image viewing modal functionality
document.addEventListener('click', function(e) {
    if (e.target.closest('.view-image-btn')) {
        const btn = e.target.closest('.view-image-btn');
        const images = JSON.parse(btn.dataset.images);
        const reportId = btn.dataset.reportId;
        const reportCompany = btn.dataset.reportCompany;
        
        showImageModal(images, reportId, reportCompany);
    }
});

function showImageModal(images, reportId, reportCompany) {
    currentImages = images;
    currentReportId = reportId;
    currentReportCompany = reportCompany;
    
    // Update modal header info
    document.getElementById('modal-report-id').textContent = `#${reportId}`;
    document.getElementById('modal-report-company').textContent = reportCompany;
    
    // Build carousel
    const carouselInner = document.getElementById('carousel-inner');
    const carouselIndicators = document.getElementById('carousel-indicators');
    const imageThumbnails = document.getElementById('image-thumbnails');
    
    carouselInner.innerHTML = '';
    carouselIndicators.innerHTML = '';
    imageThumbnails.innerHTML = '';
    
    images.forEach((image, index) => {
        const imageUrl = `/storage/${image}`;
        
        // Carousel item
        const carouselItem = document.createElement('div');
        carouselItem.className = `carousel-item ${index === 0 ? 'active' : ''}`;
        carouselItem.innerHTML = `
            <div class="text-center">
                <img src="${imageUrl}" class="d-block mx-auto" style="max-height: 400px; max-width: 100%; object-fit: contain;" alt="レポート画像">
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-outline-primary download-single-btn" data-image="${image}" data-filename="${image.split('/').pop()}">
                        <i class="fas fa-download me-1"></i>ダウンロード
                    </button>
                </div>
            </div>
        `;
        carouselInner.appendChild(carouselItem);
        
        // Carousel indicator
        const indicator = document.createElement('button');
        indicator.type = 'button';
        indicator.setAttribute('data-bs-target', '#image-carousel');
        indicator.setAttribute('data-bs-slide-to', index);
        indicator.className = index === 0 ? 'active' : '';
        indicator.setAttribute('aria-label', `Slide ${index + 1}`);
        carouselIndicators.appendChild(indicator);
        
        // Thumbnail
        const thumbnailCol = document.createElement('div');
        thumbnailCol.className = 'col-3 col-md-2 mb-2';
        thumbnailCol.innerHTML = `
            <img src="${imageUrl}" class="img-thumbnail cursor-pointer" style="height: 60px; object-fit: cover; cursor: pointer;" 
                 data-bs-target="#image-carousel" data-bs-slide-to="${index}" alt="サムネイル">
        `;
        imageThumbnails.appendChild(thumbnailCol);
    });
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

// Download functionality
document.getElementById('download-all-btn').addEventListener('click', function() {
    if (currentImages.length === 0) return;
    
    // Create a zip file for all images
    const zip = new JSZip();
    const promises = currentImages.map(async (image, index) => {
        try {
            const response = await fetch(`/storage/${image}`);
            const blob = await response.blob();
            const filename = image.split('/').pop();
            zip.file(`report_${currentReportId}_image_${index + 1}_${filename}`, blob);
        } catch (error) {
            console.error('Error downloading image:', error);
        }
    });
    
    Promise.all(promises).then(() => {
        zip.generateAsync({type: 'blob'}).then(function(content) {
            const url = window.URL.createObjectURL(content);
            const a = document.createElement('a');
            a.href = url;
            a.download = `report_${currentReportId}_images.zip`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            showNotification('画像のダウンロードが完了しました！', 'success');
        });
    });
});

// Single image download
document.addEventListener('click', function(e) {
    if (e.target.closest('.download-single-btn')) {
        const btn = e.target.closest('.download-single-btn');
        const image = btn.dataset.image;
        const filename = btn.dataset.filename;
        
        const a = document.createElement('a');
        a.href = `/storage/${image}`;
        a.download = `report_${currentReportId}_${filename}`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        
        showNotification('画像のダウンロードが完了しました！', 'success');
    }
});

// Thumbnail click to navigate carousel
document.addEventListener('click', function(e) {
    if (e.target.closest('#image-thumbnails img')) {
        const img = e.target.closest('#image-thumbnails img');
        const slideIndex = img.getAttribute('data-bs-slide-to');
        const carousel = new bootstrap.Carousel(document.getElementById('image-carousel'));
        carousel.to(parseInt(slideIndex));
    }
});
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

.animate-pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
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

/* List Group Styling */
.list-group-item.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
    transform: scale(1.02);
}

.list-group-item {
    transition: all 0.3s ease;
    border-radius: 0.5rem;
    margin-bottom: 0.25rem;
}

.list-group-item:hover {
    transform: translateX(5px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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

/* Image Modal Styling */
#imageModal .modal-dialog {
    max-width: 90vw;
}

#imageModal .modal-content {
    border-radius: 1rem;
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

#imageModal .modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom: none;
    border-radius: 1rem 1rem 0 0;
}

#imageModal .modal-title {
    font-weight: 600;
}

#imageModal .modal-body {
    padding: 2rem;
}

#imageModal .carousel {
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

#imageModal .carousel-item {
    background: #f8f9fa;
    padding: 1rem;
}

#imageModal .carousel-item img {
    border-radius: 0.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

#imageModal .carousel-control-prev,
#imageModal .carousel-control-next {
    width: 5%;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 0.5rem;
}

#imageModal .carousel-indicators {
    bottom: -2rem;
}

#imageModal .carousel-indicators button {
    background-color: #667eea;
    border-radius: 50%;
    width: 12px;
    height: 12px;
}

#image-thumbnails img {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

#image-thumbnails img:hover {
    transform: scale(1.05);
    border-color: #667eea;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.cursor-pointer {
    cursor: pointer;
}

/* Download button styling */
.download-single-btn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    color: white;
    transition: all 0.3s ease;
}

.download-single-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
    color: white;
}

#download-all-btn {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    color: white;
    transition: all 0.3s ease;
}

#download-all-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
    color: white;
}

/* Responsive image modal */
@media (max-width: 768px) {
    #imageModal .modal-dialog {
        max-width: 95vw;
        margin: 1rem;
    }
    
    #imageModal .modal-body {
        padding: 1rem;
    }
    
    #imageModal .carousel-item img {
        max-height: 300px;
    }
    
    #image-thumbnails .col-3 {
        flex: 0 0 25%;
        max-width: 25%;
    }
}

/* Image preview hover effects */
.view-image-btn {
    transition: all 0.3s ease;
}

.view-image-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

/* Modal animation */
#imageModal.fade .modal-dialog {
    transform: scale(0.8);
    transition: transform 0.3s ease;
}

#imageModal.show .modal-dialog {
    transform: scale(1);
}
</style>
@endpush 