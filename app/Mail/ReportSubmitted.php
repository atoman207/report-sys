<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;

class ReportSubmitted extends Mailable
{
    use SerializesModels;

    public $report;
    public $imageCids = [];
    public $signatureCid = null;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function build()
    {
        // Create a brief summary for the email
        $summary = $this->createSummary();
        
        return $this->subject('📋 レポート送信通知 - ' . $this->report->company . ' (' . $this->report->user->name . ')')
            ->view('emails.report_submitted')
            ->text('emails.report_submitted_text')
            ->with([
                'report' => $this->report,
                'summary' => $summary,
            ]);
    }
    
    /**
     * Create a brief summary of the report
     */
    private function createSummary()
    {
        $summary = [
            'sender' => [
                'name' => $this->report->user->name ?? 'Unknown User',
                'email' => $this->report->user->email ?? 'No email',
                'role' => $this->report->user->role ?? 'User'
            ],
            'report' => [
                'id' => $this->report->id,
                'company' => $this->report->company,
                'work_type' => $this->report->work_type,
                'task_type' => $this->report->task_type,
                'visit_status' => $this->report->visit_status,
                'created_at' => $this->report->created_at->format('Y年m月d日 H:i'),
                'image_count' => count($this->report->images ?? []),
                'has_signature' => !empty($this->report->signature),
                'total_size' => $this->report->formatted_image_size ?? '0 MB'
            ],
            'quick_info' => [
                'person' => $this->report->person,
                'site' => $this->report->site,
                'store' => $this->report->store,
                'start_time' => $this->report->start_time ? $this->report->start_time->format('H:i') : 'N/A',
                'end_time' => $this->report->end_time ? $this->report->end_time->format('H:i') : 'N/A'
            ]
        ];
        
        return $summary;
    }
}
