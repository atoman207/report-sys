@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1 fs-4 fw-bold text-primary">
                        <i class="fas fa-images me-2"></i>レポート画像ギャラリー
                    </h2>
                    <div class="text-muted small">レポート #{{ $report->id }} の画像管理・プレビュー・ダウンロード</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.images.downloadReport', $report->id) }}" class="btn btn-success btn-animated">
                        <i class="fas fa-download me-1"></i>全画像ダウンロード
                    </a>
                    <a href="{{ route('admin.images.index') }}" class="btn btn-outline-secondary btn-animated">
                        <i class="fas fa-arrow-left me-1"></i>画像管理に戻る
                    </a>
                </div>
            </div>

            <!-- Report Information -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-file-alt me-2"></i>レポート情報
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <strong>レポートID:</strong> 
                                <span class="badge bg-secondary">#{{ $report->id }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>会社名:</strong> 
                                <span class="fw-medium">{{ $report->company }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>送信者:</strong> 
                                <div class="d-flex align-items-center mt-1">
                                    <div class="user-avatar me-2">
                                        <img src="{{ $report->user->avatar_url ?? asset('images/default-avatar.png') }}" 
                                             alt="{{ $report->user ? $report->user->getAvatarDisplayName() : 'Unknown User' }}" 
                                             class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;"
                                             onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $report->user->name ?? '不明' }}</div>
                                        <div class="small text-muted">{{ $report->user->email ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <strong>作成日時:</strong> 
                                <small>{{ $report->created_at->format('Y-m-d H:i:s') }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Main Image Display -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-image me-2"></i>レポート画像</span>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-secondary" id="prevBtn" onclick="previousImage()">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" id="nextBtn" onclick="nextImage()">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <div id="mainImageContainer" class="mb-3">
                                <!-- Main image will be loaded here -->
                            </div>
                            
                            <!-- Image Navigation Dots -->
                            <div class="d-flex justify-content-center gap-2 mb-3" id="imageDots">
                                <!-- Dots will be generated here -->
                            </div>
                            
                            <!-- Image Actions -->
                            <div class="d-flex justify-content-center gap-2">
                                <a href="#" id="downloadBtn" class="btn btn-success btn-animated">
                                    <i class="fas fa-download me-1"></i>↓ ダウンロード
                                </a>
                                <button type="button" class="btn btn-danger btn-animated" onclick="deleteCurrentImage()">
                                    <i class="fas fa-trash me-1"></i>削除
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image Thumbnails -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-th me-2"></i>画像サムネイル ({{ count($allImages) }}枚)
                </div>
                <div class="card-body">
                    <div class="row g-3" id="thumbnailsContainer">
                        <!-- Thumbnails will be generated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>画像削除の確認
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
            </div>
            <div class="modal-body">
                <p>この画像を削除してもよろしいですか？この操作は元に戻せません。</p>
                <p class="text-muted small">ファイル名: <span id="deleteFileName"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">削除</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Debug: Log the data being passed from server
console.log('Debug - allImages:', @json($allImages));
console.log('Debug - report:', @json($report));

// Image data from the server
const images = @json($allImages);
const reportId = {{ $report->id }};
let currentImageIndex = 0;

// Initialize the gallery
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing gallery');
    console.log('Images count:', images.length);
    console.log('Images:', images);
    
    if (images.length > 0) {
        console.log('Loading first image...');
        loadImage(0);
        generateThumbnails();
        generateDots();
        updateNavigationButtons();
    } else {
        console.log('No images, showing message');
        showNoImagesMessage();
    }
});

// Load image at specific index
function loadImage(index) {
    console.log('loadImage called with index:', index);
    console.log('images array:', images);
    console.log('images length:', images.length);
    
    if (index < 0 || index >= images.length) {
        console.log('Index out of range');
        return;
    }
    
    currentImageIndex = index;
    const image = images[index];
    console.log('Loading image:', image);
    
    // Update main image
    const container = document.getElementById('mainImageContainer');
    container.innerHTML = `
        <img id="mainImage" src="{{ route('admin.images.file', ['path' => ':imagePath']) }}" 
             alt="${image.filename}" 
             class="img-fluid rounded shadow" 
             style="max-height: 500px; max-width: 100%; object-fit: contain;"
             onload="console.log('Image loaded successfully:', this.src)"
             onerror="console.log('Image failed to load:', this.src)">
    `;
    
    // Update download button
    const downloadBtn = document.getElementById('downloadBtn');
    downloadBtn.href = `{{ route('admin.images.download', ['reportId' => ':reportId', 'imagePath' => ':imagePath']) }}`
        .replace(':reportId', reportId)
        .replace(':imagePath', encodeURIComponent(image.path));

    // Update main image src to stream route
    const mainImg = document.getElementById('mainImage');
    if (mainImg) {
        mainImg.src = `{{ route('admin.images.file', ['path' => ':imagePath']) }}`.replace(':imagePath', encodeURIComponent(image.path));
    }
    
    // Update active states
    updateActiveStates();
    updateNavigationButtons();
}

// Generate thumbnail grid
function generateThumbnails() {
    const container = document.getElementById('thumbnailsContainer');
    container.innerHTML = '';
    
    images.forEach((image, index) => {
        const col = document.createElement('div');
        col.className = 'col-md-3 col-lg-2 col-sm-4 col-6';
        
        col.innerHTML = `
            <div class="thumbnail-item ${index === 0 ? 'active' : ''}" onclick="loadImage(${index})">
                <img src="${image.url}" 
                     alt="${image.filename}" 
                     class="img-thumbnail thumbnail-img"
                     onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                <div class="thumbnail-overlay">
                    <small class="text-white">${image.filename}</small>
                </div>
            </div>
        `;
        
        container.appendChild(col);
    });
}

// Generate navigation dots
function generateDots() {
    const container = document.getElementById('imageDots');
    container.innerHTML = '';
    
    images.forEach((_, index) => {
        const dot = document.createElement('button');
        dot.className = `btn btn-sm ${index === 0 ? 'btn-primary' : 'btn-outline-primary'}`;
        dot.style.cssText = 'width: 12px; height: 12px; border-radius: 50%; padding: 0; margin: 0 2px;';
        dot.onclick = () => loadImage(index);
        container.appendChild(dot);
    });
}

// Update active states
function updateActiveStates() {
    // Update thumbnails
    document.querySelectorAll('.thumbnail-item').forEach((item, index) => {
        item.classList.toggle('active', index === currentImageIndex);
    });
    
    // Update dots
    document.querySelectorAll('#imageDots .btn').forEach((dot, index) => {
        dot.classList.toggle('btn-primary', index === currentImageIndex);
        dot.classList.toggle('btn-outline-primary', index !== currentImageIndex);
    });
}

// Update navigation buttons
function updateNavigationButtons() {
    document.getElementById('prevBtn').disabled = currentImageIndex === 0;
    document.getElementById('nextBtn').disabled = currentImageIndex === images.length - 1;
}

// Navigation functions
function previousImage() {
    if (currentImageIndex > 0) {
        loadImage(currentImageIndex - 1);
    }
}

function nextImage() {
    if (currentImageIndex < images.length - 1) {
        loadImage(currentImageIndex + 1);
    }
}

// Delete current image
function deleteCurrentImage() {
    if (images.length === 0) return;
    
    const image = images[currentImageIndex];
    document.getElementById('deleteFileName').textContent = image.filename;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Confirm delete
function confirmDelete() {
    const image = images[currentImageIndex];
    const deleteUrl = `{{ route('admin.images.delete', ['reportId' => ':reportId', 'imagePath' => ':imagePath']) }}`
        .replace(':reportId', reportId)
        .replace(':imagePath', encodeURIComponent(image.path));
    
    // Create form for DELETE request
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = deleteUrl;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    
    form.appendChild(csrfToken);
    form.appendChild(methodField);
    document.body.appendChild(form);
    
    // Submit form
    form.submit();
}

// Show no images message
function showNoImagesMessage() {
    const container = document.getElementById('mainImageContainer');
    container.innerHTML = `
        <div class="text-center text-muted py-5">
            <i class="fas fa-images fa-3x mb-3"></i>
            <p class="fs-5">このレポートには画像がありません。</p>
        </div>
    `;
    
    document.getElementById('thumbnailsContainer').innerHTML = `
        <div class="col-12 text-center text-muted py-4">
            <i class="fas fa-images fa-2x mb-2"></i>
            <p>画像がありません</p>
        </div>
    `;
}
</script>
@endpush

@push('styles')
<style>
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

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    font-weight: 600;
    color: #495057;
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

/* Thumbnail Styling */
.thumbnail-item {
    position: relative;
    cursor: pointer;
    border-radius: 0.5rem;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.thumbnail-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.thumbnail-item.active {
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.thumbnail-img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    transition: all 0.3s ease;
}

.thumbnail-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 4px 8px;
    opacity: 0;
    transition: opacity 0.3s ease;
    text-align: center;
    font-size: 0.75rem;
}

.thumbnail-item:hover .thumbnail-overlay {
    opacity: 1;
}

/* User Avatar Styling */
.user-avatar {
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

/* Badge Styling */
.badge {
    font-size: 0.75em;
    border-radius: 0.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .thumbnail-img {
        height: 100px;
    }
    
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
    }
}
</style>
@endpush 