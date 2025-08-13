<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\AdminImageController;

// Authentication routes
Auth::routes();

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Form routes
    Route::get('/form', [RequestController::class, 'showForm'])->name('showForm');
    Route::post('/form', [RequestController::class, 'submitForm'])->name('submitForm');
    
    // Report management routes
    Route::get('/home', [RequestController::class, 'indexReport'])->name('indexReport');
    Route::get('/my-reports', [RequestController::class, 'myReports'])->name('myReports');
    Route::get('/report/{id}/edit', [RequestController::class, 'editReport'])->name('editReport');
    Route::post('/report/{id}/update', [RequestController::class, 'updateReport'])->name('updateReport');
    Route::delete('/report/{id}', [RequestController::class, 'deleteReport'])->name('deleteReport');
    
    // Owner information routes
    Route::get('/owner/edit', [RequestController::class, 'editOwner'])->name('owner.edit');
    Route::post('/owner/update', [RequestController::class, 'updateOwner'])->name('owner.update');
    
    // Admin management routes
    Route::post('/admin/make-admin', [RequestController::class, 'makeUserAdmin'])->name('admin.makeAdmin');
    Route::post('/admin/remove-admin', [RequestController::class, 'removeUserAdmin'])->name('admin.removeAdmin');
    
    // Dashboard routes (role-based)
    Route::get('/dashboard', [RequestController::class, 'dashboard'])->name('dashboard')->middleware('admin');
    Route::get('/user-dashboard', [RequestController::class, 'userDashboard'])->name('userDashboard');
    
    // Admin image management routes
    Route::prefix('admin/images')->middleware(['admin'])->group(function () {
        Route::get('/', [AdminImageController::class, 'index'])->name('admin.images.index');
        Route::get('/preview/{reportId}/{imagePath}', [AdminImageController::class, 'preview'])->where('imagePath', '.*')->name('admin.images.preview');
        Route::get('/download/{reportId}/{imagePath}', [AdminImageController::class, 'download'])->where('imagePath', '.*')->name('admin.images.download');
        Route::get('/download-report/{reportId}', [AdminImageController::class, 'downloadReportImages'])->name('admin.images.downloadReport');
        Route::get('/download-all', [AdminImageController::class, 'downloadAllImages'])->name('admin.images.downloadAll');
        Route::delete('/delete/{reportId}/{imagePath}', [AdminImageController::class, 'deleteImage'])->where('imagePath', '.*')->name('admin.images.delete');
        // Stream image files directly (works even without storage:link)
        Route::get('/file/{path}', [AdminImageController::class, 'file'])->where('path', '.*')->name('admin.images.file');
    });
    
    // Debug route for testing (remove in production)
    Route::get('/debug/report/{id}', function($id) {
        $report = App\Models\Report::find($id);
        if (!$report) {
            return 'Report not found';
        }
        return [
            'id' => $report->id,
            'images' => $report->images,
            'signature' => $report->signature,
            'storage_exists' => [
                'images' => $report->images ? array_map(function($img) { 
                    return ['path' => $img, 'exists' => Storage::disk('public')->exists($img)]; 
                }, $report->images) : [],
                'signature' => $report->signature ? ['path' => $report->signature, 'exists' => Storage::disk('public')->exists($report->signature)] : null
            ]
        ];
    });
});

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
}); 