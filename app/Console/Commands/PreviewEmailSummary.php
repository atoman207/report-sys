<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Report;
use App\Mail\ReportSubmitted;

class PreviewEmailSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:preview-summary {--report-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Preview the email summary format for a report';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reportId = $this->option('report-id');
        
        if ($reportId) {
            $report = Report::with('user')->find($reportId);
            if (!$report) {
                $this->error("Report with ID {$reportId} not found.");
                return 1;
            }
        } else {
            // Get the latest report
            $report = Report::with('user')->latest()->first();
            if (!$report) {
                $this->error("No reports found in the database.");
                return 1;
            }
        }
        
        $this->info("📧 Email Summary Preview for Report #{$report->id}");
        $this->line("=" . str_repeat("=", 50));
        $this->line("");
        
        // Create the email instance to get the summary
        $email = new ReportSubmitted($report);
        
        // Use reflection to access the private method
        $reflection = new \ReflectionClass($email);
        $method = $reflection->getMethod('createSummary');
        $method->setAccessible(true);
        $summary = $method->invoke($email);
        
        $this->info("📊 レポート概要");
        $this->line("送信者: {$summary['sender']['name']} ({$summary['sender']['email']})");
        $this->line("送信日時: {$summary['report']['created_at']}");
        $this->line("レポートID: #{$summary['report']['id']}");
        $this->line("");
        
        $this->info("基本情報:");
        $this->line("- 会社名: {$summary['report']['company']}");
        $this->line("- 担当者: {$summary['quick_info']['person']}");
        $this->line("- 工事分類: {$summary['report']['work_type']}");
        $this->line("- 作業分類: {$summary['report']['task_type']}");
        $this->line("- 訪問ステータス: {$summary['report']['visit_status']}");
        $this->line("- 作業時間: {$summary['quick_info']['start_time']} - {$summary['quick_info']['end_time']}");
        $this->line("");
        
        $this->info("添付ファイル:");
        $this->line("- 画像: {$summary['report']['image_count']}枚");
        $this->line("- ファイルサイズ: {$summary['report']['total_size']}");
        $this->line("- 署名: " . ($summary['report']['has_signature'] ? 'あり' : 'なし'));
        $this->line("");
        
        $this->info("📧 Email Subject:");
        $this->line("📋 レポート送信通知 - {$summary['report']['company']} ({$summary['sender']['name']})");
        $this->line("");
        
        $this->info("✅ Summary format is ready for email sending!");
        
        return 0;
    }
} 