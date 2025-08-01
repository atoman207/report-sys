<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>レポート送信通知</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .summary-section {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            border-left: 5px solid #2196f3;
        }
        .summary-title {
            font-size: 20px;
            font-weight: 600;
            color: #1976d2;
            margin-bottom: 20px;
            text-align: center;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        .summary-item {
            background: white;
            padding: 12px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .summary-label {
            font-weight: 600;
            color: #1976d2;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .summary-value {
            color: #333;
            font-size: 14px;
            font-weight: 500;
        }
        .sender-info {
            background: #f3e5f5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #9c27b0;
        }
        .sender-info strong {
            color: #7b1fa2;
        }
        .section {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        .section:last-child {
            border-bottom: none;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #4e73df;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e9ecef;
        }
        .field {
            margin-bottom: 12px;
        }
        .field-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        .field-value {
            color: #6c757d;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #4e73df;
        }
        .images-section {
            margin-top: 20px;
        }
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .image-item {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .image-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .signature-section {
            margin-top: 20px;
            text-align: center;
        }
        .signature-image {
            max-width: 200px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 10px;
            background: white;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin-top: 20px;
        }
        .btn:hover {
            background: linear-gradient(135deg, #224abe, #4e73df);
        }
        .timestamp {
            color: #6c757d;
            font-size: 12px;
            text-align: center;
            margin-top: 15px;
        }
        .quick-stats {
            display: flex;
            justify-content: space-around;
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 18px;
            font-weight: 600;
            color: #4e73df;
        }
        .stat-label {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>📋 レポート送信通知</h1>
        </div>
        
        <div class="content">
            <!-- Brief Summary Section -->
            <div class="summary-section">
                <div class="summary-title">📊 レポート概要</div>
                
                <!-- Prominent Sender Information -->
                <div class="sender-info" style="background: linear-gradient(135deg, #fff3cd, #ffeaa7); border-left: 5px solid #ffc107; margin-bottom: 20px;">
                    <div style="font-size: 16px; font-weight: 600; color: #856404; margin-bottom: 10px;">
                        📧 <strong>送信者情報</strong>
                    </div>
                    <div style="font-size: 14px; color: #856404;">
                        <strong>名前:</strong> {{ $summary['sender']['name'] }}
                        <br>
                        <strong>メールアドレス:</strong> <span style="color: #e74c3c; font-weight: 600;">{{ $summary['sender']['email'] }}</span>
                        <br>
                        <strong>役割:</strong> {{ $summary['sender']['role'] }}
                        <br>
                        <strong>送信日時:</strong> {{ $summary['report']['created_at'] }}
                        <br>
                        <strong>レポートID:</strong> #{{ $summary['report']['id'] }}
                    </div>
                </div>
                
                <!-- Brief Description -->
                <div class="sender-info" style="background: linear-gradient(135deg, #d1ecf1, #bee5eb); border-left: 5px solid #17a2b8; margin-bottom: 20px;">
                    <div style="font-size: 16px; font-weight: 600; color: #0c5460; margin-bottom: 10px;">
                        📋 <strong>レポート概要</strong>
                    </div>
                    <div style="font-size: 14px; color: #0c5460;">
                        <strong>会社名:</strong> {{ $summary['report']['company'] }}
                        <br>
                        <strong>作業内容:</strong> {{ $summary['report']['work_type'] }} - {{ $summary['report']['task_type'] }}
                        <br>
                        <strong>訪問状況:</strong> {{ $summary['report']['visit_status'] }}
                        <br>
                        <strong>添付ファイル:</strong> 画像{{ $summary['report']['image_count'] }}枚{{ $summary['report']['has_signature'] ? ' + 署名' : '' }}
                    </div>
                </div>
                
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-label">会社名</div>
                        <div class="summary-value">{{ $summary['report']['company'] }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">担当者</div>
                        <div class="summary-value">{{ $summary['quick_info']['person'] }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">工事分類</div>
                        <div class="summary-value">{{ $summary['report']['work_type'] }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">作業分類</div>
                        <div class="summary-value">{{ $summary['report']['task_type'] }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">訪問ステータス</div>
                        <div class="summary-value">{{ $summary['report']['visit_status'] }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">作業時間</div>
                        <div class="summary-value">{{ $summary['quick_info']['start_time'] }} - {{ $summary['quick_info']['end_time'] }}</div>
                    </div>
                </div>
                
                <div class="quick-stats">
                    <div class="stat-item">
                        <div class="stat-number">{{ $summary['report']['image_count'] }}</div>
                        <div class="stat-label">画像</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $summary['report']['total_size'] }}</div>
                        <div class="stat-label">ファイルサイズ</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $summary['report']['has_signature'] ? '✓' : '✗' }}</div>
                        <div class="stat-label">署名</div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">🏢 基本情報</div>
                <div class="field">
                    <div class="field-label">会社名</div>
                    <div class="field-value">{{ $report->company }}</div>
                </div>
                <div class="field">
                    <div class="field-label">担当者名</div>
                    <div class="field-value">{{ $report->person }}</div>
                </div>
                <div class="field">
                    <div class="field-label">現場・店舗</div>
                    <div class="field-value">{{ $report->site ?? 'N/A' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">店舗名</div>
                    <div class="field-value">{{ $report->store ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">🔧 作業情報</div>
                <div class="field">
                    <div class="field-label">工事分類</div>
                    <div class="field-value">{{ $report->work_type }}</div>
                </div>
                <div class="field">
                    <div class="field-label">作業分類</div>
                    <div class="field-value">{{ $report->task_type }}</div>
                </div>
                <div class="field">
                    <div class="field-label">依頼内容</div>
                    <div class="field-value">{{ $report->request_detail ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">⏰ 時間情報</div>
                <div class="field">
                    <div class="field-label">作業開始時間</div>
                    <div class="field-value">{{ $report->start_time ? $report->start_time->format('Y年m月d日 H:i') : 'N/A' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">作業終了時間</div>
                    <div class="field-value">{{ $report->end_time ? $report->end_time->format('Y年m月d日 H:i') : 'N/A' }}</div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">📍 訪問情報</div>
                <div class="field">
                    <div class="field-label">訪問ステータス</div>
                    <div class="field-value">{{ $report->visit_status }}</div>
                </div>
                <div class="field">
                    <div class="field-label">修理場所</div>
                    <div class="field-value">{{ $report->repair_place ?? 'N/A' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">訪問時状況</div>
                    <div class="field-value">{{ $report->visit_status_detail ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">📝 作業詳細</div>
                <div class="field">
                    <div class="field-label">作業内容</div>
                    <div class="field-value">{{ $report->work_detail ?? 'N/A' }}</div>
                </div>
            </div>

            @php use Illuminate\Support\Facades\Storage; @endphp
            @if($report->images && count($report->images) > 0)
            <div class="section">
                <div class="section-title">📸 添付画像 ({{ count($report->images) }}枚)</div>
                <div class="images-section">
                    <div class="image-grid" style="display:flex">
                        @foreach($report->images as $image)
                        <div class="image-item" style="width:40%">
                            <img src="{{ $message->embed(Storage::disk('public')->path($image)) }}" alt="Report Image" style="height: auto; object-fit: cover;">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            @if($report->signature)
            <div class="section">
                <div class="section-title">✍️ 署名</div>
                <div class="signature-section">
                    <img src="{{ $message->embed(Storage::disk('public')->path($report->signature)) }}" alt="Signature" class="signature-image">
                </div>
            </div>
            @endif

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/dashboard') }}" class="btn">管理画面で確認する</a>
                <br><br>
                <a href="{{ url('/report/' . $report->id . '/edit') }}" class="btn" style="background: linear-gradient(135deg, #28a745, #20c997);">レポートを編集する</a>
            </div>

            <div class="timestamp">
                このメールは自動送信されています。返信はできません。
            </div>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} レポート管理システム. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
