<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequestController;

// Authentication routes
Auth::routes();

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Form routes
    Route::get('/form', [RequestController::class, 'showForm'])->name('showForm');
    Route::post('/form', [RequestController::class, 'submitForm'])->name('submitForm');
    
    // Report management routes
    Route::get('/home', [RequestController::class, 'indexReport'])->name('indexReport');
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
});

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
}); 