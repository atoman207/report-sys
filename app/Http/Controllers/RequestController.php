<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Report;
use App\Models\User;
use App\Mail\ReportSubmitted;
use App\Services\ImageUploadService;

class RequestController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    public function showForm()
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('dashboard')->with('error', '管理者は新規レポートを作成できません。');
        }
        return view('request_form');
    }

    public function submitForm(Request $request)
    {
        // Check if user has already submitted a report today
        $today = now()->startOfDay();
        $existingReport = Report::where('user_id', auth()->id())
            ->whereDate('created_at', $today)
            ->first();

        if ($existingReport) {
            return back()->withInput()->with('error', '本日は既にレポートを提出済みです。1日1回までレポートを提出できます。');
        }

        // Validate form data
        $request->validate([
            'company' => 'required|string|max:255',
            'person' => 'required|string|max:255',
            'work_type' => 'required|string|max:255',
            'task_type' => 'required|string|max:255',
            'visit_status' => 'required|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,gif|max:2048', // 2MB max per file
            'signature' => 'nullable|string',
        ]);

        // Check image limits
        $images = $request->file('images', []);
        $totalSize = 0;
        $maxImages = 10;
        $maxTotalSize = 5 * 1024 * 1024; // 5MB

        if (count($images) > $maxImages) {
            return back()->withInput()->with('error', "画像は最大{$maxImages}枚までアップロードできます。");
        }

        foreach ($images as $image) {
            $totalSize += $image->getSize();
        }

        if ($totalSize > $maxTotalSize) {
            return back()->withInput()->with('error', '画像の合計サイズは5MB以下にしてください。');
        }

        try {
            // Create report
            $report = Report::create([
                'user_id' => auth()->id(),
                'company' => $request->company,
                'person' => $request->person,
                'site' => $request->site,
                'store' => $request->store,
                'work_type' => $request->work_type,
                'task_type' => $request->task_type,
                'request_detail' => $request->request_detail,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'visit_status' => $request->visit_status,
                'repair_place' => $request->repair_place,
                'visit_status_detail' => $request->visit_status_detail,
                'work_detail' => $request->work_detail,
            ]);

            // Upload images using the service
            if (!empty($images)) {
                $imagePaths = $this->imageUploadService->uploadImages($images, $report->id);
                $report->update(['images' => $imagePaths]);
            }

            // Save signature using the service
            if ($request->signature) {
                $signaturePath = $this->imageUploadService->uploadSignature($request->signature, $report->id);
                $report->update(['signature' => $signaturePath]);
            }

            // Send email notification to all administrators
            try {
                // Use hardcoded admin emails as specified
                $adminEmails = [
                    'goodsman207@gmail.com',
                    'daise2ac@gmail.com',
                    'okadakaido810@gmail.com'
                ];
                
                if (!empty($adminEmails)) {
                    Mail::to($adminEmails)->send(new ReportSubmitted($report));
                    
                    // Log successful email sending
                    \Log::info('Report notification sent successfully', [
                        'report_id' => $report->id,
                        'admin_emails' => $adminEmails,
                        'user_id' => auth()->id(),
                        'user_email' => auth()->user()->email,
                        'company' => $report->company
                    ]);
                } else {
                    \Log::warning('No admin emails found for report notification', [
                        'report_id' => $report->id,
                        'user_id' => auth()->id()
                    ]);
                }
            } catch (\Exception $e) {
                // Log email sending error but don't fail the report submission
                \Log::error('Failed to send report notification email', [
                    'report_id' => $report->id,
                    'error' => $e->getMessage(),
                    'user_id' => auth()->id(),
                    'user_email' => auth()->user()->email
                ]);
            }

            // Prepare success message with details
            $imageCount = count($images);
            $signatureText = $request->signature ? ' + 署名' : '';
            $successMessage = "レポートが正常に送信されました。";
            
            if ($imageCount > 0) {
                $successMessage .= "画像{$imageCount}枚{$signatureText}が添付されました。";
            }
            
            $successMessage .= "管理者に通知メールが送信されました。";

            return back()->with('success', $successMessage);

        } catch (\Exception $e) {
            \Log::error('Failed to submit report', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'request_data' => $request->except(['images', 'signature'])
            ]);

            return back()->withInput()->with('error', 'レポートの送信に失敗しました。もう一度お試しください。');
        }
    }

    public function indexReport()
    {
        // Only admins can access this page
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('showForm')->with('error', '権限がありません');
        }
        
        $reports = Report::orderBy('created_at', 'desc')->get();
        return view('report.index', compact('reports'));
    }

    public function myReports()
    {
        $userId = auth()->id();
        $reports = Report::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('report.my_reports', compact('reports'));
    }

    public function editReport($id)
    {
        $report = Report::findOrFail($id);
        return view('report.edit', compact('report'));
    }

    public function updateReport(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        // Check permissions - users can only edit their own reports, admins can edit any
        if (auth()->user()->role !== 'admin' && $report->user_id !== auth()->id()) {
            return back()->with('error', 'このレポートを編集する権限がありません。');
        }

        try {
            $request->validate([
                'company' => 'required|string|max:255',
                'person' => 'required|string|max:255',
                'work_type' => 'required|string|max:255',
                'task_type' => 'required|string|max:255',
                'visit_status' => 'required|string|max:255',
            ]);

            $report->fill($request->except(['_token', '_method']));
            $report->save();

            // Log the update
            \Log::info('Report updated successfully', [
                'report_id' => $report->id,
                'user_id' => auth()->id(),
                'updated_fields' => $request->except(['_token', '_method'])
            ]);

            $redirectRoute = auth()->user()->role === 'admin' ? 'dashboard' : 'myReports';
            return redirect()->route($redirectRoute)->with('success', 'レポートが正常に更新されました。');

        } catch (\Exception $e) {
            \Log::error('Failed to update report', [
                'report_id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->withInput()->with('error', 'レポートの更新に失敗しました。もう一度お試しください。');
        }
    }

    public function deleteReport($id)
    {
        $report = Report::findOrFail($id);
        
        // Only admins can delete
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'レポートを削除する権限がありません。');
        }

        try {
            // Delete associated images and signature
            if (!empty($report->images)) {
                $this->imageUploadService->deleteImages($report->images);
            }
            
            if ($report->signature) {
                $this->imageUploadService->deleteAvatar($report->signature);
            }

            $reportCompany = $report->company;
            $report->delete();

            // Log the deletion
            \Log::info('Report deleted successfully', [
                'report_id' => $id,
                'deleted_by_user_id' => auth()->id(),
                'report_company' => $reportCompany
            ]);

            return redirect()->route('dashboard')->with('success', "レポート「{$reportCompany}」が正常に削除されました。");

        } catch (\Exception $e) {
            \Log::error('Failed to delete report', [
                'report_id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'レポートの削除に失敗しました。もう一度お試しください。');
        }
    }

    public function dashboard(Request $request)
    {
        // Get all users with their report counts
        $users = User::withCount('reports')->get();
        
        // Get reports based on user filter with eager loading
        $reportsQuery = Report::with('user')->orderBy('created_at', 'desc');
        
        if ($request->has('user_id') && $request->user_id) {
            $reportsQuery->where('user_id', $request->user_id);
            $selectedUser = User::find($request->user_id);
        } else {
            $selectedUser = null;
        }
        
        $reports = $reportsQuery->get();
        
        // Verify image accessibility and add debugging info
        $reports->each(function ($report) {
            if ($report->hasImages()) {
                $imageAccessibility = [];
                foreach ($report->images as $imagePath) {
                    $imageAccessibility[] = [
                        'path' => $imagePath,
                        'exists' => \Storage::disk('public')->exists($imagePath),
                        'url' => \Storage::disk('public')->url($imagePath),
                        'size' => \Storage::disk('public')->exists($imagePath) ? \Storage::disk('public')->size($imagePath) : 0
                    ];
                }
                // Store as a temporary property that won't conflict with model accessors
                $report->setAttribute('_image_accessibility', $imageAccessibility);
            }
        });
        
        // Add image statistics
        $imageStats = [
            'total_reports_with_images' => $reports->filter(fn($r) => $r->hasImages())->count(),
            'total_images' => $reports->sum('image_count'),
            'total_image_size' => $reports->sum('total_image_size'),
            'reports_with_signatures' => $reports->filter(fn($r) => !empty($r->signature))->count(),
            'accessible_images' => $reports->filter(fn($r) => $r->hasImages())->sum(function($r) {
                return collect($r->getAttribute('_image_accessibility') ?? [])->filter(fn($img) => $img['exists'])->count();
            }),
        ];
        
        return view('dashboard', compact('reports', 'users', 'selectedUser', 'imageStats'));
    }

    public function userDashboard()
    {
        $userId = auth()->id();
        
        // Get today's reports
        $todayReports = Report::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get this month's reports
        $monthReports = Report::where('user_id', $userId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get();
        
        // Get total reports
        $totalReports = Report::where('user_id', $userId)->get();
        
        // Get recent reports (last 7 days)
        $recentReports = Report::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('user_dashboard', compact('todayReports', 'monthReports', 'totalReports', 'recentReports'));
    }

    public function editOwner()
    {
        $user = auth()->user();
        return view('owner.edit', compact('user'));
    }

    public function updateOwner(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        
        $user->save();

        return redirect()->route('userDashboard')->with('success', 'オーナー情報を更新しました。');
    }

    public function makeUserAdmin(Request $request)
    {
        // Only admins can make other users admin
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', '管理者権限の付与には管理者権限が必要です。');
        }

        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $user = User::findOrFail($request->user_id);
            
            // Prevent making yourself admin if you're already admin
            if ($user->id === auth()->id()) {
                return back()->with('warning', '自分自身の権限は変更できません。');
            }

            $user->role = 'admin';
            $user->save();

            // Log the action
            \Log::info('User promoted to admin', [
                'promoted_user_id' => $user->id,
                'promoted_by_user_id' => auth()->id(),
                'user_name' => $user->name,
                'user_email' => $user->email
            ]);

            return back()->with('success', "ユーザー「{$user->name}」を管理者に昇格しました。");

        } catch (\Exception $e) {
            \Log::error('Failed to promote user to admin', [
                'user_id' => $request->user_id,
                'promoted_by_user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->with('error', '管理者権限の付与に失敗しました。もう一度お試しください。');
        }
    }

    public function removeUserAdmin(Request $request)
    {
        // Only admins can remove admin status
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', '管理者権限の削除には管理者権限が必要です。');
        }

        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $user = User::findOrFail($request->user_id);
            
            // Prevent removing your own admin status
            if ($user->id === auth()->id()) {
                return back()->with('warning', '自分自身の権限は変更できません。');
            }
            
            // Prevent removing the default admin
            if ($user->email === 'zumado.jp0527@gmail.com') {
                return back()->with('error', 'デフォルト管理者の権限は削除できません。');
            }

            $user->role = 'user';
            $user->save();

            // Log the action
            \Log::info('User demoted from admin', [
                'demoted_user_id' => $user->id,
                'demoted_by_user_id' => auth()->id(),
                'user_name' => $user->name,
                'user_email' => $user->email
            ]);

            return back()->with('success', "ユーザー「{$user->name}」の管理者権限を削除しました。");

        } catch (\Exception $e) {
            \Log::error('Failed to demote user from admin', [
                'user_id' => $request->user_id,
                'demoted_by_user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->with('error', '管理者権限の削除に失敗しました。もう一度お試しください。');
        }
    }
} 