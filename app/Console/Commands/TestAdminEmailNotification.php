<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Report;
use App\Models\User;
use App\Mail\ReportSubmitted;
use Illuminate\Support\Facades\Mail;

class TestAdminEmailNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:admin-email {--report-id= : Specific report ID to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test admin email notification system with specific administrator emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Admin Email Notification Test ===');
        
        // Get the report to test with
        $reportId = $this->option('report-id');
        
        if ($reportId) {
            $report = Report::with('user')->find($reportId);
            if (!$report) {
                $this->error("Report with ID {$reportId} not found.");
                return 1;
            }
        } else {
            // Get the most recent report
            $report = Report::with('user')->latest()->first();
            if (!$report) {
                $this->error("No reports found in the database.");
                return 1;
            }
        }

        $this->info("Testing with Report ID: {$report->id}");
        $this->info("Company: {$report->company}");
        $this->info("User: {$report->user->name} ({$report->user->email})");
        $this->info("Created: {$report->created_at->format('Y-m-d H:i:s')}");
        
        // Define admin emails
        $adminEmails = [
            'daise2ac@ibaraki.email.ne.jp',
            'd2d_hachiouji@icloud.com',
            'daise2denko@themis.ocn.ne.jp',
            'goodsman@gmail.com'
        ];
        
        $this->info("\n📧 Admin Emails:");
        foreach ($adminEmails as $email) {
            $this->line("  - {$email}");
        }
        
        $this->info("\n📋 Email Content Preview:");
        $this->line("Subject: 📋 レポート送信通知 - {$report->company} ({$report->user->name})");
        $this->line("Sender: {$report->user->name} ({$report->user->email})");
        $this->line("Report ID: #{$report->id}");
        $this->line("Company: {$report->company}");
        $this->line("Work Type: {$report->work_type} - {$report->task_type}");
        $this->line("Visit Status: {$report->visit_status}");
        $this->line("Images: " . count($report->images ?? []) . " files");
        $this->line("Signature: " . ($report->signature ? 'Yes' : 'No'));
        
        if ($this->confirm('Do you want to send a test email to all administrators?')) {
            try {
                Mail::to($adminEmails)->send(new ReportSubmitted($report));
                
                $this->info("\n✅ Test email sent successfully!");
                $this->info("📧 Email sent to:");
                foreach ($adminEmails as $email) {
                    $this->line("  - {$email}");
                }
                
                $this->info("\n📊 Email Details:");
                $this->line("  - Report ID: {$report->id}");
                $this->line("  - Sender: {$report->user->name} ({$report->user->email})");
                $this->line("  - Company: {$report->company}");
                $this->line("  - Work Type: {$report->work_type}");
                $this->line("  - Task Type: {$report->task_type}");
                $this->line("  - Visit Status: {$report->visit_status}");
                $this->line("  - Images: " . count($report->images ?? []) . " files");
                $this->line("  - Signature: " . ($report->signature ? 'Yes' : 'No'));
                
                return 0;
            } catch (\Exception $e) {
                $this->error("\n❌ Failed to send test email:");
                $this->error($e->getMessage());
                return 1;
            }
        } else {
            $this->info("\n⏭️ Test email cancelled.");
            return 0;
        }
    }
} 