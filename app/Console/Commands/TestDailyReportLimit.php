<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;

class TestDailyReportLimit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:daily-report-limit {--user-id= : Specific user ID to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the daily report limit functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Daily Report Limit Test ===');
        
        // Get user to test with
        $userId = $this->option('user-id');
        
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found.");
                return 1;
            }
        } else {
            // Get the first user
            $user = User::first();
            if (!$user) {
                $this->error("No users found in the database.");
                return 1;
            }
        }

        $this->info("Testing with user: {$user->name} (ID: {$user->id})");
        
        // Check today's reports
        $today = now()->startOfDay();
        $todayReports = Report::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->get();
        
        $this->info("\n📊 Today's Reports:");
        $this->line("  - Total reports today: {$todayReports->count()}");
        
        if ($todayReports->count() > 0) {
            $this->line("  - Reports:");
            foreach ($todayReports as $report) {
                $this->line("    • ID: {$report->id} - {$report->company} ({$report->created_at->format('H:i')})");
            }
        }
        
        // Test the limit logic
        $existingReport = Report::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->first();
        
        if ($existingReport) {
            $this->warn("\n⚠️  User has already submitted a report today!");
            $this->line("  - Report ID: {$existingReport->id}");
            $this->line("  - Company: {$existingReport->company}");
            $this->line("  - Submitted: {$existingReport->created_at->format('Y-m-d H:i:s')}");
            $this->line("  - Status: Would be blocked from submitting another report");
        } else {
            $this->info("\n✅ User has not submitted a report today");
            $this->line("  - Status: Would be allowed to submit a report");
        }
        
        // Show recent reports (last 7 days)
        $recentReports = Report::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->get();
        
        $this->info("\n📅 Recent Reports (Last 7 Days):");
        foreach ($recentReports as $report) {
            $date = $report->created_at->format('Y-m-d');
            $time = $report->created_at->format('H:i');
            $isToday = $report->created_at->isToday() ? ' (TODAY)' : '';
            $this->line("  - {$date} {$time}: {$report->company}{$isToday}");
        }
        
        // Test the validation logic
        $this->info("\n🧪 Testing Validation Logic:");
        
        if ($existingReport) {
            $this->line("  - User would see: '本日は既にレポートを提出済みです。1日1回までレポートを提出できます。'");
            $this->line("  - Form would be disabled");
            $this->line("  - Dashboard would show submitted status");
        } else {
            $this->line("  - User would be allowed to submit a report");
            $this->line("  - Form would be enabled");
            $this->line("  - Dashboard would show pending status");
        }
        
        $this->info("\n✅ Daily report limit test completed!");
        
        return 0;
    }
} 