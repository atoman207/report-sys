# レポート管理システム (Report Management System)

## 概要
このシステムは、現場作業のレポート作成・管理・共有を行うためのWebアプリケーションです。モバイルファーストのデザインで、スマートフォンでの使用に最適化されています。

## 主要機能

### 🔐 認証・権限管理
- **多管理者対応**: 複数の管理者を設定可能
- **デフォルト管理者**: `zumado.jp0527@gmail.com` がデフォルト管理者
- **ロールベースアクセス**: 管理者と一般ユーザーの権限分離
- **管理者管理機能**: ダッシュボードから管理者権限の付与・削除が可能

### 📧 自動メール通知
- **リアルタイム通知**: レポート送信時に全管理者に自動メール送信
- **リッチメール**: HTML形式で画像・署名を含む美しいメールテンプレート
- **添付ファイル**: 画像と署名をメールに添付
- **詳細情報**: 送信者情報、日時、全レポートデータを含む

### 📱 モバイルファーストUI
- **レスポンシブデザイン**: スマートフォン・タブレット・PCに対応
- **タッチフレンドリー**: タッチ操作に最適化されたインターフェース
- **画像プレビュー**: アップロード前の画像プレビュー機能
- **署名機能**: タッチ対応の電子署名パッド

### 📊 ダッシュボード機能

#### 管理者ダッシュボード
- **メンバー一覧**: 全ユーザーとレポート数の表示
- **管理者管理**: 管理者権限の付与・削除機能
- **統計情報**: 総レポート数、今日・今月のレポート数、画像付きレポート数
- **詳細表示**: 送信者、画像、署名を含む完全なレポート情報
- **フィルタリング**: ユーザー別レポート表示

#### ユーザーダッシュボード
- **今日のレポート**: 当日作成したレポートの表示
- **統計情報**: 今日・今月・総レポート数
- **最近のレポート**: 過去7日間のレポート履歴
- **クイックアクセス**: 新規レポート作成への直接リンク

### 📝 レポート機能
- **包括的フォーム**: 会社情報、作業内容、時間、画像、署名を含む
- **画像アップロード**: 最大10枚、プレビュー機能付き
- **電子署名**: タッチ対応の署名パッド
- **バリデーション**: 入力値の検証とエラーハンドリング

## 技術仕様

### バックエンド
- **フレームワーク**: Laravel 11
- **データベース**: MySQL
- **認証**: Laravel Breeze
- **メール**: Laravel Mail with HTML templates
- **ファイルストレージ**: Laravel Storage (public disk)

### フロントエンド
- **CSSフレームワーク**: Bootstrap 5.3
- **アイコン**: Font Awesome 6.0
- **署名**: SignaturePad.js
- **レスポンシブ**: モバイルファーストCSS

### データベース設計
```sql
-- Users Table
users (
    id, name, email, password, role, email_verified_at, 
    remember_token, created_at, updated_at
)

-- Reports Table
reports (
    id, company, person, site, store, work_type, task_type,
    request_detail, start_time, end_time, visit_status,
    repair_place, visit_status_detail, work_detail,
    signature, images, user_id, created_at, updated_at
)
```

## セットアップ

### 1. 環境要件
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js (開発用)

### 2. インストール
```bash
# リポジトリのクローン
git clone [repository-url]
cd daise2denko

# 依存関係のインストール
composer install
npm install

# 環境設定
cp .env.example .env
php artisan key:generate

# データベース設定
# .envファイルでDB設定を編集

# マイグレーション実行
php artisan migrate

# シーダー実行（デフォルト管理者作成）
php artisan db:seed

# ストレージリンク作成
php artisan storage:link

# 開発サーバー起動
php artisan serve
```

### 3. 管理者設定
```bash
# 既存ユーザーを管理者に設定
php artisan user:make-admin [email]

# 管理者権限削除（デフォルト管理者以外）
php artisan user:remove-admin [email]
```

## 使用方法

### 管理者としてログイン
1. `zumado.jp0527@gmail.com` でログイン
2. 管理ダッシュボードにアクセス
3. メンバー管理・レポート確認・管理者権限管理が可能

### 一般ユーザーとして使用
1. アカウント作成・ログイン
2. マイダッシュボードでレポート確認
3. 新規レポート作成・編集

### レポート作成
1. 「新規レポート作成」ボタンをクリック
2. 必要情報を入力
3. 画像をアップロード（プレビュー可能）
4. 電子署名を入力
5. 送信（自動的に管理者にメール通知）

## メール設定

### 環境変数設定
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=zumado.jp0527@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=zumado.jp0527@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Gmail設定
1. Gmailアカウントで2段階認証を有効化
2. アプリパスワードを生成
3. 上記の環境変数に設定

## セキュリティ機能

- **CSRF保護**: 全フォームにCSRFトークン
- **入力検証**: サーバーサイドバリデーション
- **ファイルアップロード制限**: 画像形式・サイズ制限
- **権限チェック**: ロールベースアクセス制御
- **SQLインジェクション対策**: Eloquent ORM使用

## パフォーマンス最適化

- **画像最適化**: 適切なサイズ制限とプレビュー
- **レスポンシブ画像**: モバイル対応の画像表示
- **効率的なクエリ**: Eloquentリレーション使用
- **キャッシュ対応**: Laravelキャッシュ機能対応

## トラブルシューティング

### よくある問題

1. **画像が表示されない**
   - `php artisan storage:link` を実行
   - ストレージディスクの権限を確認

2. **メールが送信されない**
   - メール設定を確認
   - Gmailアプリパスワードを確認

3. **管理者権限が付与されない**
   - データベースの`role`カラムを確認
   - マイグレーションを再実行

### ログ確認
```bash
# Laravelログ
tail -f storage/logs/laravel.log

# メールログ
tail -f storage/logs/mail.log
```

## 開発者向け情報

### コード構造
```
app/
├── Http/Controllers/RequestController.php  # メインコントローラー
├── Models/
│   ├── User.php                           # ユーザーモデル
│   └── Report.php                         # レポートモデル
├── Mail/ReportSubmitted.php               # メールクラス
└── Console/Commands/MakeUserAdmin.php     # 管理者設定コマンド

resources/views/
├── layouts/app.blade.php                  # メインレイアウト
├── dashboard.blade.php                    # 管理者ダッシュボード
├── user_dashboard.blade.php               # ユーザーダッシュボード
├── request_form.blade.php                 # レポート作成フォーム
└── emails/report_submitted.blade.php      # メールテンプレート
```

### カスタマイズ

#### 新しいフィールド追加
1. マイグレーションファイルを作成
2. Reportモデルの`$fillable`配列に追加
3. フォームビューにフィールド追加
4. バリデーションルール追加

#### メールテンプレート変更
`resources/views/emails/report_submitted.blade.php` を編集

#### スタイル変更
`resources/css/app.css` を編集

## ライセンス
このプロジェクトはMITライセンスの下で公開されています。

## サポート
技術的な問題や質問がある場合は、開発チームまでお問い合わせください。
