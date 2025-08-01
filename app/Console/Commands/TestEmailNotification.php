<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Report;
use App\Models\User;
use App\Mail\ReportSubmitted;
use Illuminate\Support\Facades\Mail;

class TestEmailNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-notification {--report-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the email notification system for report submissions';

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
        
        $this->info("Testing email notification for Report #{$report->id} ({$report->company})");
        $this->line("Sender: {$report->user->name} ({$report->user->email})");
        $this->line("Created: {$report->created_at->format('Y-m-d H:i')}");
        $this->line("Images: " . count($report->images ?? []) . " files");
        $this->line("Signature: " . ($report->signature ? 'Yes' : 'No'));
        $this->line("");
        
        // Get admin emails
        $adminEmails = User::where('role', 'admin')->pluck('email')->toArray();
        
        if (empty($adminEmails)) {
            $this->error("No admin users found. Please create an admin user first.");
            return 1;
        }
        
        $this->info("Sending test email to admin users:");
        foreach ($adminEmails as $email) {
            $this->line("  - {$email}");
        }
        $this->line("");
        
        try {
            Mail::to($adminEmails)->send(new ReportSubmitted($report));
            $this->info("✅ Test email sent successfully!");
            $this->line("Check the admin email inboxes for the test email.");
        } catch (\Exception $e) {
            $this->error("❌ Failed to send test email: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
} 