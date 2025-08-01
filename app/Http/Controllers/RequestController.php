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
        if (auth()->user()->role === 'admin') {
            return redirect()->route('dashboard')->with('error', '管理者は新規レポートを作成できません。');
        }
        // Validation
        $request->validate([
            'company' => 'required|string|max:255',
            'person' => 'required|string|max:255',
            'work_type' => 'required|string',
            'task_type' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'visit_status' => 'required|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // 10MB per file
        ]);

        // Additional validation for image limits
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            
            // Check maximum number of images
            if (count($images) > 10) {
                return back()->withErrors(['images' => '最大10枚までアップロードできます。'])->withInput();
            }
            
            // Check total file size (5MB = 5 * 1024 * 1024 bytes)
            $totalSize = 0;
            foreach ($images as $image) {
                $totalSize += $image->getSize();
            }
            
            if ($totalSize > 5 * 1024 * 1024) {
                return back()->withErrors(['images' => '合計ファイルサイズが5MBを超えています。'])->withInput();
            }
        }

        $data = $request->except(['_token', 'images', 'signature']);
        $data['user_id'] = auth()->id(); // Associate with current user

        // Create the report first to get the ID
        $report = Report::create($data);

        // Save images using the service
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            
            // Validate each image
            foreach ($images as $image) {
                if (!$this->imageUploadService->validateImage($image)) {
                    return back()->withErrors(['images' => '無効な画像ファイルが含まれています。'])->withInput();
                }
            }
            
            // Upload images using the service
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
            $adminEmails = User::where('role', 'admin')->pluck('email')->toArray();
            if (!empty($adminEmails)) {
                Mail::to($adminEmails)->send(new ReportSubmitted($report));
                
                // Log successful email sending
                \Log::info('Report notification sent successfully', [
                    'report_id' => $report->id,
                    'admin_emails' => $adminEmails,
                    'user_id' => auth()->id(),
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
                'user_id' => auth()->id()
            ]);
        }

        return back()->with('success', 'レポートが正常に送信されました。管理者に通知メールが送信されました。');
    }

    public function indexReport()
    {
        $reports = Report::orderBy('created_at', 'desc')->get();
        return view('report.index', compact('reports'));
    }

    public function editReport($id)
    {
        $report = Report::findOrFail($id);
        return view('report.edit', compact('report'));
    }

    public function updateReport(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $report->fill($request->except(['_token']))->save();
        return redirect()->route('indexReport')->with('success', '更新しました');
    }

    public function deleteReport($id)
    {
        $report = Report::findOrFail($id);
        
        // Only admins can delete
        if (auth()->user()->role === 'admin') {
            $report->delete();
            return redirect()->route('indexReport')->with('success', '削除しました');
        }
        
        return redirect()->route('indexReport')->with('error', '権限がありません');
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
            return back()->with('error', '権限がありません');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->role = 'admin';
        $user->save();

        return back()->with('success', "ユーザー '{$user->name}' を管理者にしました。");
    }

    public function removeUserAdmin(Request $request)
    {
        // Only admins can remove admin status
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', '権限がありません');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        
        // Prevent removing the default admin
        if ($user->email === 'zumado.jp0527@gmail.com') {
            return back()->with('error', 'デフォルト管理者は削除できません。');
        }

        $user->role = 'user';
        $user->save();

        return back()->with('success', "ユーザー '{$user->name}' の管理者権限を削除しました。");
    }
} 