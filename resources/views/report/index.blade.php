@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0">
                        <i class="fas fa-list me-2"></i>依頼一覧
                    </h4>
                </div>
                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success m-3">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger m-3">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif
                    
                    <!-- Mobile View: Card Layout -->
                    <div class="d-block d-md-none">
                        @forelse($reports as $report)
                            <div class="card m-3 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-building me-1"></i>{{ $report->company }}
                                        </h6>
                                        <span class="badge bg-secondary">#{{ $report->id }}</span>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>担当者
                                            </small>
                                            <div>{{ $report->person }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">
                                                <i class="fas fa-tools me-1"></i>工事分類
                                            </small>
                                            <div>{{ $report->work_type }}</div>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <small class="text-muted">
                                                <i class="fas fa-tasks me-1"></i>作業分類
                                            </small>
                                            <div>{{ $report->task_type }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>開始時間
                                            </small>
                                            <div>{{ \Carbon\Carbon::parse($report->start_time)->format('m/d H:i') }}</div>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>終了時間
                                            </small>
                                            <div>{{ \Carbon\Carbon::parse($report->end_time)->format('m/d H:i') }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('editReport', $report->id) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                            <i class="fas fa-edit me-1"></i>編集
                                        </a>
                                        @if(auth()->user()->role === 'admin')
                                            <form action="{{ route('deleteReport', $report->id) }}" method="POST" class="flex-fill">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('本当に削除しますか？')">
                                                    <i class="fas fa-trash me-1"></i>削除
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>依頼がありません。</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <!-- Desktop View: Table Layout -->
                    <div class="d-none d-md-block">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>会社名</th>
                                        <th>担当者</th>
                                        <th>工事分類</th>
                                        <th>作業分類</th>
                                        <th>開始</th>
                                        <th>終了</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reports as $report)
                                        <tr>
                                            <td><span class="badge bg-secondary">#{{ $report->id }}</span></td>
                                            <td>{{ $report->company }}</td>
                                            <td>{{ $report->person }}</td>
                                            <td>{{ $report->work_type }}</td>
                                            <td>{{ $report->task_type }}</td>
                                            <td>{{ \Carbon\Carbon::parse($report->start_time)->format('m/d H:i') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($report->end_time)->format('m/d H:i') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('editReport', $report->id) }}" class="btn btn-outline-primary">
                                                        <i class="fas fa-edit me-1"></i>編集
                                                    </a>
                                                    @if(auth()->user()->role === 'admin')
                                                        <form action="{{ route('deleteReport', $report->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('本当に削除しますか？')">
                                                                <i class="fas fa-trash me-1"></i>削除
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                依頼がありません。
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media (max-width: 768px) {
    .card-body {
        padding: 1rem !important;
    }
}

.card {
    border-radius: 1rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.btn-group-sm .btn {
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
}

.badge {
    font-size: 0.75em;
    border-radius: 0.5rem;
}
</style>
@endpush 