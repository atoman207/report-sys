📋 レポート送信通知
===============================

📧 送信者情報
===============================
名前: {{ $summary['sender']['name'] }}
メールアドレス: {{ $summary['sender']['email'] }}
役割: {{ $summary['sender']['role'] }}
送信日時: {{ $summary['report']['created_at'] }}
レポートID: #{{ $summary['report']['id'] }}

📋 レポート概要
===============================
会社名: {{ $summary['report']['company'] }}
作業内容: {{ $summary['report']['work_type'] }} - {{ $summary['report']['task_type'] }}
訪問状況: {{ $summary['report']['visit_status'] }}
担当者: {{ $summary['quick_info']['person'] }}
作業時間: {{ $summary['quick_info']['start_time'] }} - {{ $summary['quick_info']['end_time'] }}

添付ファイル:
- 画像: {{ $summary['report']['image_count'] }}枚
- ファイルサイズ: {{ $summary['report']['total_size'] }}
- 署名: {{ $summary['report']['has_signature'] ? 'あり' : 'なし' }}

詳細情報:
===============================

🏢 基本情報
- 会社名: {{ $report->company }}
- 担当者名: {{ $report->person }}
- 現場・店舗: {{ $report->site ?? 'N/A' }}
- 店舗名: {{ $report->store ?? 'N/A' }}

🔧 作業情報
- 工事分類: {{ $report->work_type }}
- 作業分類: {{ $report->task_type }}
- 依頼内容: {{ $report->request_detail ?? 'N/A' }}

⏰ 時間情報
- 作業開始時間: {{ $report->start_time ? $report->start_time->format('Y年m月d日 H:i') : 'N/A' }}
- 作業終了時間: {{ $report->end_time ? $report->end_time->format('Y年m月d日 H:i') : 'N/A' }}

📍 訪問情報
- 訪問ステータス: {{ $report->visit_status }}
- 修理場所: {{ $report->repair_place ?? 'N/A' }}
- 訪問時状況: {{ $report->visit_status_detail ?? 'N/A' }}

📝 作業詳細
- 作業内容: {{ $report->work_detail ?? 'N/A' }}

@if($report->images && count($report->images) > 0)
📸 添付画像 ({{ count($report->images) }}枚)
@foreach($report->images as $index => $image)
- 画像{{ $index + 1 }}: {{ $image }}
@endforeach
@endif

@if($report->signature)
✍️ 署名: {{ $report->signature }}
@endif

===============================

管理画面で確認する: {{ url('/dashboard') }}
レポートを編集する: {{ url('/report/' . $report->id . '/edit') }}

===============================
このメールは自動送信されています。返信はできません。
© {{ date('Y') }} レポート管理システム. All rights reserved. 