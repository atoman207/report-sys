@extends('layouts.app')

@section('content')
<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content success-modal">
            <div class="modal-body text-center p-5">
                <div class="success-icon mb-4">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4 class="modal-title mb-3" id="successModalLabel">送信完了！</h4>
                <p class="text-muted mb-4">レポートが正常に送信されました。<br>管理者に通知メールが送信されました。</p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-primary btn-animated" data-bs-dismiss="modal">
                        <i class="fas fa-home me-2"></i>メインページへ
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-animated" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>閉じる
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-4">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-animated">
                    <i class="fas fa-arrow-left me-2"></i>ダッシュボードに戻る
                </a>
            </div>
            <div class="card form-card animate-slide-up" data-delay="0">
                <div class="card-header form-header">
                    <div class="d-flex align-items-center">
                        <div class="header-icon me-3">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <h4 class="mb-1">作業報告フォーム</h4>
                            <p class="text-white-50 mb-0">作業内容を正確に記入してください</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success animate-fade-in" data-delay="0.1">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger animate-fade-in" data-delay="0.1">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form id="submit_report_form" method="POST" action="{{ route('submitForm') }}" enctype="multipart/form-data">
                        @csrf
                        <!-- 基本情報 -->
                        <div class="form-section animate-slide-up" data-delay="0.2">
                            <div class="section-header">
                                <i class="fas fa-info-circle me-2"></i>
                                <span>基本情報</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">会社名</label>
                                    <input type="text" name="company" class="form-control form-input" value="{{ old('company') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">担当者名</label>
                                    <input type="text" name="person" class="form-control form-input" value="{{ old('person') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">現場名</label>
                                    <input type="text" name="site" class="form-control form-input" value="{{ old('site') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">店舗名</label>
                                    <input type="text" name="store" class="form-control form-input" value="{{ old('store') }}">
                                </div>
                            </div>
                        </div>
                        <!-- 作業分類 -->
                        <div class="form-section animate-slide-up" data-delay="0.3">
                            <div class="section-header">
                                <i class="fas fa-tasks me-2"></i>
                                <span>作業分類</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">工事分類</label>
                                    <select name="work_type" class="form-select form-input" required>
                                        <option value="">選択してください</option>
                                        <option value="エアコン" {{ old('work_type') == 'エアコン' ? 'selected' : '' }}>エアコン</option>
                                        <option value="ダクト" {{ old('work_type') == 'ダクト' ? 'selected' : '' }}>ダクト</option>
                                        <option value="電気" {{ old('work_type') == '電気' ? 'selected' : '' }}>電気</option>
                                        <option value="その他" {{ old('work_type') == 'その他' ? 'selected' : '' }}>その他</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">作業分類</label>
                                    <select name="task_type" class="form-select form-input" required>
                                        <option value="">選択してください</option>
                                        <option value="点検調査" {{ old('task_type') == '点検調査' ? 'selected' : '' }}>点検調査</option>
                                        <option value="修理" {{ old('task_type') == '修理' ? 'selected' : '' }}>修理</option>
                                        <option value="入替" {{ old('task_type') == '入替' ? 'selected' : '' }}>入替</option>
                                        <option value="納品" {{ old('task_type') == '納品' ? 'selected' : '' }}>納品</option>
                                        <option value="その他" {{ old('task_type') == 'その他' ? 'selected' : '' }}>その他</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="form-label">依頼内容</label>
                                <textarea name="request_detail" class="form-control form-input" rows="2">{{ old('request_detail') }}</textarea>
                            </div>
                        </div>
                        <!-- 作業時間 -->
                        <div class="form-section animate-slide-up" data-delay="0.4">
                            <div class="section-header">
                                <i class="fas fa-clock me-2"></i>
                                <span>作業時間</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">作業開始時間</label>
                                    <input type="datetime-local" name="start_time" class="form-control form-input" value="{{ old('start_time') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">作業終了時間</label>
                                    <input type="datetime-local" name="end_time" class="form-control form-input" value="{{ old('end_time') }}" required>
                                </div>
                            </div>
                        </div>
                        <!-- 訪問情報 -->
                        <div class="form-section animate-slide-up" data-delay="0.5">
                            <div class="section-header">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <span>訪問情報</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">訪問ステータス</label>
                                    <select name="visit_status" class="form-select form-input" required>
                                        <option value="">選択してください</option>
                                        <option value="見積提出" {{ old('visit_status') == '見積提出' ? 'selected' : '' }}>見積提出</option>
                                        <option value="完了" {{ old('visit_status') == '完了' ? 'selected' : '' }}>完了</option>
                                        <option value="対応不可" {{ old('visit_status') == '対応不可' ? 'selected' : '' }}>対応不可</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">修理場所</label>
                                    <input type="text" name="repair_place" class="form-control form-input" value="{{ old('repair_place') }}">
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="form-label">訪問時状況</label>
                                <textarea name="visit_status_detail" class="form-control form-input" rows="2">{{ old('visit_status_detail') }}</textarea>
                            </div>
                        </div>
                        <!-- 作業詳細 -->
                        <div class="form-section animate-slide-up" data-delay="0.6">
                            <div class="section-header">
                                <i class="fas fa-tools me-2"></i>
                                <span>作業詳細</span>
                            </div>
                            <div class="mt-3">
                                <label class="form-label">作業内容</label>
                                <textarea name="work_detail" class="form-control form-input" rows="3">{{ old('work_detail') }}</textarea>
                            </div>
                        </div>
                        <!-- サイン・画像添付 -->
                        <div class="form-section animate-slide-up" data-delay="0.7">
                            <div class="section-header">
                                <i class="fas fa-signature me-2"></i>
                                <span>サイン・画像添付</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">サイン（指で記入）</label>
                                <div class="mb-2">
                                    <canvas id="signature-pad" class="signature-canvas"></canvas>
                                    <input type="hidden" name="signature" id="signature-input">
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm btn-animated" id="clear-signature">
                                            <i class="fas fa-eraser me-1"></i>クリア
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm btn-animated" id="undo-signature">
                                            <i class="fas fa-undo me-1"></i>元に戻す
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">画像添付（最大10枚、合計5MBまで）</label>
                                <div class="image-upload-container">
                                    <div class="upload-status mb-2">
                                        <span id="image-count" class="badge bg-info">0/10</span>
                                        <span id="size-info" class="badge bg-secondary ms-2">0MB/5MB</span>
                                    </div>
                                    <input type="file" name="images[]" id="image-input" class="form-control form-input" accept="image/*" multiple>
                                    <div class="form-text">JPEG, PNG, JPG, GIF形式、1ファイル最大10MBまで</div>
                                    <div id="image-preview-container" class="mt-3" style="display: none;">
                                        <div id="image-preview-grid" class="row g-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 送信ボタン -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg submit-btn blue-button" id="submitButton">
                                <i class="fas fa-paper-plane me-2"></i>送信する
                            </button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.6/dist/signature_pad.umd.min.js"></script>
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

        // Success modal for form submission
        @if(session('success'))
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            setTimeout(() => {
                successModal.show();
            }, 500);
        @endif

        // Form submission with loading animation
        const form = document.getElementById('submit_report_form');
        const submitBtn = document.querySelector('.submit-btn');
        
        form.addEventListener('submit', function(e) {
            // Show loading state
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="loading me-2"></span>送信中...';
            submitBtn.disabled = true;
            
            // Re-enable after a delay (in case of validation errors)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });

        // Signature Pad
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas, {
            minWidth: 0.5,
            maxWidth: 2.5,
            throttle: 16,
            velocityFilterWeight: 0.7,
            penColor: '#000000'
        });

        // Responsive canvas sizing
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            const rect = canvas.getBoundingClientRect();
            
            canvas.width = rect.width * ratio;
            canvas.height = rect.height * ratio;
            canvas.style.width = rect.width + 'px';
            canvas.style.height = rect.height + 'px';
            
            signaturePad.clear();
        }

        // Initial resize
        resizeCanvas();

        // Resize on window resize
        window.addEventListener('resize', resizeCanvas);

        // Form submission
        document.querySelector('#submit_report_form').addEventListener('submit', function(e) {
            if (!signaturePad.isEmpty()) {
                document.getElementById('signature-input').value = signaturePad.toDataURL('image/png');
            }
        });

        // Clear button
        document.getElementById('clear-signature').addEventListener('click', function() {
            signaturePad.clear();
        });

        // Undo button
        document.getElementById('undo-signature').addEventListener('click', function() {
            const data = signaturePad.toData();
            if (data.length > 0) {
                data.pop();
                signaturePad.fromData(data);
            }
        });

        // Prevent scrolling when drawing on mobile
        canvas.addEventListener('touchstart', function(e) {
            e.preventDefault();
        }, { passive: false });

        canvas.addEventListener('touchmove', function(e) {
            e.preventDefault();
        }, { passive: false });

        // 画像プレビュー
        const imageInput = document.getElementById('image-input');
        const previewContainer = document.getElementById('image-preview-container');
        const previewGrid = document.getElementById('image-preview-grid');
        const imageCount = document.getElementById('image-count');
        const sizeInfo = document.getElementById('size-info');
        let selectedFiles = [];
        const MAX_IMAGES = 10;
        const MAX_TOTAL_SIZE = 5 * 1024 * 1024; // 5MB in bytes
        const MAX_FILE_SIZE = 2 * 1024 * 1024; // 10MB per file

        imageInput.addEventListener('change', function(e) {
            const newFiles = Array.from(e.target.files);
            const currentCount = selectedFiles.length;
            const availableSlots = MAX_IMAGES - currentCount;
            
            // Check if we can add more files
            if (newFiles.length > availableSlots) {
                alert(`最大${MAX_IMAGES}枚までアップロードできます。現在${currentCount}枚選択中です。`);
                return;
            }
            
            // Validate file sizes and add valid files
            const validFiles = [];
            let totalSize = selectedFiles.reduce((sum, file) => sum + file.size, 0);
            
            for (let file of newFiles) {
                if (file.size > MAX_FILE_SIZE) {
                    alert(`ファイル "${file.name}" が10MBを超えています。`);
                    continue;
                }
                
                if (totalSize + file.size > MAX_TOTAL_SIZE) {
                    alert(`合計サイズが5MBを超えるため、ファイル "${file.name}" を追加できません。`);
                    continue;
                }
                
                validFiles.push(file);
                totalSize += file.size;
            }
            
            // Add valid files to selected files
            selectedFiles = [...selectedFiles, ...validFiles];
            updateImagePreview();
            updateUploadStatus();
        });

        function updateUploadStatus() {
            const count = selectedFiles.length;
            const totalSize = selectedFiles.reduce((sum, file) => sum + file.size, 0);
            const totalSizeMB = (totalSize / (1024 * 1024)).toFixed(2);
            
            imageCount.textContent = `${count}/${MAX_IMAGES}`;
            sizeInfo.textContent = `${totalSizeMB}MB/5MB`;
            
            // Update badge colors based on limits
            if (count >= MAX_IMAGES) {
                imageCount.className = 'badge bg-danger';
            } else if (count >= MAX_IMAGES * 0.8) {
                imageCount.className = 'badge bg-warning';
            } else {
                imageCount.className = 'badge bg-info';
            }
            
            if (totalSize >= MAX_TOTAL_SIZE) {
                sizeInfo.className = 'badge bg-danger ms-2';
            } else if (totalSize >= MAX_TOTAL_SIZE * 0.8) {
                sizeInfo.className = 'badge bg-warning ms-2';
            } else {
                sizeInfo.className = 'badge bg-secondary ms-2';
            }
        }

        function updateImagePreview() {
            previewGrid.innerHTML = '';
            if (selectedFiles.length === 0) {
                previewContainer.style.display = 'none';
                return;
            }
            previewContainer.style.display = 'block';
            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-md-3';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Preview';
                    img.style = 'width:100%;height:90px;object-fit:cover;border-radius:0.7rem;border:1.5px solid #d0e3fa;cursor:zoom-in;';
                    img.setAttribute('data-fullsrc', e.target.result);
                    // Attach double-click event for modal preview
                    img.addEventListener('dblclick', function() {
                        const modalImg = document.getElementById('modal-image');
                        modalImg.src = this.getAttribute('data-fullsrc');
                        const modal = new bootstrap.Modal(document.getElementById('image-modal'));
                        modal.show();
                    });
                    col.innerHTML = `
                        <div class="image-preview-item position-relative mb-2"></div>
                    `;
                    col.querySelector('.image-preview-item').appendChild(img);
                    col.querySelector('.image-preview-item').innerHTML += `
                        <button type="button" class="remove-image btn btn-danger btn-sm position-absolute top-0 start-0 m-1" data-index="${index}" title="削除">×</button>
                        <div class="image-info small mt-1">
                            <div class="image-name">${file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name}</div>
                            <div class="image-size">${(file.size/1024).toFixed(1)} KB</div>
                        </div>
                    `;
                    previewGrid.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
            setTimeout(() => {
                document.querySelectorAll('.remove-image').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const idx = parseInt(this.getAttribute('data-index'));
                        selectedFiles.splice(idx, 1);
                        updateFileInput();
                        updateImagePreview();
                        updateUploadStatus();
                    });
                });
            }, 100);
            // Ensure modal image src is cleared when modal is closed
            const imageModal = document.getElementById('image-modal');
            if (!imageModal.hasAttribute('data-listener')) {
                imageModal.setAttribute('data-listener', 'true');
                imageModal.addEventListener('hidden.bs.modal', function() {
                    document.getElementById('modal-image').src = '';
                });
            }
        }

        function updateFileInput() {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            imageInput.files = dataTransfer.files;
        }
        
        // Initialize upload status
        updateUploadStatus();
    });
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
/* Modern Card Styling */
.form-card {
    border-radius: 1.5rem;
    border: none;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    overflow: hidden;
}

.form-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
}

.form-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 2rem;
}

.header-icon {
    font-size: 2.5rem;
    opacity: 0.9;
}

/* Form Sections */
.form-section {
    margin-bottom: 2.5rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 1rem;
    border-left: 4px solid #667eea;
    transition: all 0.3s ease;
}

.form-section:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.15);
}

.section-header {
    display: flex;
    align-items: center;
    font-weight: 600;
    font-size: 1.1rem;
    color: #495057;
    margin-bottom: 1.5rem;
}

.section-header i {
    color: #667eea;
    font-size: 1.2rem;
}

/* Form Inputs */
.form-input {
    border-radius: 0.75rem;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    background: white;
}

.form-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    transform: translateY(-2px);
}

/* Button Animations */
.btn-animated {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border-radius: 0.75rem;
}

.btn-animated:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.btn-animated:active {
    transform: translateY(0);
}

.submit-btn {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    padding: 1rem 3rem;
    font-weight: 600;
    border-radius: 1rem;
    color: white;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

.submit-btn:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
    color: white;
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

/* Signature Canvas */
.signature-canvas {
    width: 100%;
    height: 180px;
    border: 2px solid #e9ecef;
    border-radius: 0.75rem;
    background: white;
    transition: all 0.3s ease;
}

.signature-canvas:hover {
    border-color: #667eea;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.15);
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

/* Success Modal */
.success-modal {
    border-radius: 1.5rem;
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.success-icon {
    font-size: 4rem;
    color: #28a745;
    animation: bounceIn 0.6s ease;
}

@keyframes bounceIn {
    0% {
        transform: scale(0.3);
        opacity: 0;
    }
    50% {
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Cool Button Animation Styles */
.cool-button {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    transform-origin: center;
    color: white;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

/* Keep only the blue-button style for fixed, visible button */
.blue-button {
    position: fixed !important;
    z-index: 9999 !important;
    left: 50% !important;
    bottom: 32px !important;
    transform: translateX(-50%) !important;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
    border: none !important;
    color: white !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2) !important;
    box-shadow: 0 8px 32px rgba(0, 123, 255, 0.25) !important;
    min-width: 220px !important;
    max-width: 90vw !important;
    padding: 1rem 2.5rem !important;
    opacity: 1 !important;
    display: block !important;
    visibility: visible !important;
    border-radius: 1rem !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
}

.blue-button:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%) !important;
    box-shadow: 0 12px 40px rgba(0, 123, 255, 0.4) !important;
    transform: translateX(-50%) translateY(-2px) !important;
}

@media (max-width: 576px) {
    .blue-button {
        min-width: 90vw !important;
        padding: 1rem 1rem !important;
        font-size: 1.1rem !important;
        bottom: 20px !important;
    }
}

/* Alert Styling */
.alert {
    border-radius: 1rem;
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-header {
        padding: 1.5rem;
    }
    
    .header-icon {
        font-size: 2rem;
    }
    
    .form-section {
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .submit-btn {
        padding: 0.75rem 2rem;
    }
    
    .cool-button {
        min-height: 48px;
        font-size: 16px;
    }
}

/* Upload Status Styling */
.upload-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.upload-status .badge {
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
    font-weight: 500;
}

.image-upload-container {
    position: relative;
}

.image-preview-item {
    position: relative;
    transition: all 0.3s ease;
}

.image-preview-item:hover {
    transform: scale(1.05);
}

.remove-image {
    opacity: 0.8;
    transition: all 0.2s ease;
}

.remove-image:hover {
    opacity: 1;
    transform: scale(1.1);
}

.image-info {
    background: rgba(255, 255, 255, 0.9);
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
}

.image-name {
    font-weight: 500;
    color: #495057;
}

.image-size {
    color: #6c757d;
    font-size: 0.7rem;
}

/* Progress indicators for upload limits */
.badge.bg-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
}

.badge.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
    color: #212529 !important;
}

.badge.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
}

.badge.bg-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
}
</style>
@endpush 