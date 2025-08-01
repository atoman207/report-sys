@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>依頼編集
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('updateReport', $report->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-building me-1"></i>対応会社名
                            </label>
                            <input type="text" name="company" class="form-control form-control-lg" value="{{ old('company', $report->company) }}" required placeholder="会社名を入力">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-user me-1"></i>担当者名
                            </label>
                            <input type="text" name="person" class="form-control form-control-lg" value="{{ old('person', $report->person) }}" required placeholder="担当者名を入力">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-tools me-1"></i>工事分類
                            </label>
                            <input type="text" name="work_type" class="form-control form-control-lg" value="{{ old('work_type', $report->work_type) }}" required placeholder="工事分類を入力">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-tasks me-1"></i>作業分類
                            </label>
                            <input type="text" name="task_type" class="form-control form-control-lg" value="{{ old('task_type', $report->task_type) }}" required placeholder="作業分類を入力">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-clock me-1"></i>作業開始時間
                            </label>
                            <input type="datetime-local" name="start_time" class="form-control form-control-lg" value="{{ old('start_time', $report->start_time) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-clock me-1"></i>作業終了時間
                            </label>
                            <input type="datetime-local" name="end_time" class="form-control form-control-lg" value="{{ old('end_time', $report->end_time) }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-file-alt me-1"></i>依頼内容
                            </label>
                            <textarea name="request_detail" class="form-control form-control-lg" rows="3" placeholder="依頼内容を入力">{{ old('request_detail', $report->request_detail) }}</textarea>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>更新
                            </button>
                            <a href="{{ route('indexReport') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>戻る
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media (max-width: 576px) {
    .card-body {
        padding: 1.5rem 1rem !important;
    }
    
    .form-control-lg {
        font-size: 16px !important; /* Prevents zoom on iOS */
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
}

.card {
    border-radius: 1rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
}
</style>
@endpush 