<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ProjectController;

// Redirect root to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/doAdminLogin', [AuthController::class, 'doLogin'])->name('do.admin_login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset routes
Route::get('/forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Protected admin panel routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.home');
    Route::get('/admin/users', [UserController::class, 'index'])->name('users');
    Route::post('/doAddUser', [UserController::class, 'store'])->name('do.add_user');
    Route::put('/admin/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/admin/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Donors / Partners routes
    Route::get('/admin/donors', [DonorController::class, 'index'])->name('donors.index');
    Route::post('/admin/donors', [DonorController::class, 'store'])->name('donors.store');
    Route::put('/admin/donors/{id}', [DonorController::class, 'update'])->name('donors.update');
    Route::delete('/admin/donors/{id}', [DonorController::class, 'destroy'])->name('donors.destroy');

    // Applications routes
    Route::get('/admin/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/admin/applications/all', [ApplicationController::class, 'showAll'])->name('applications.all');
    Route::get('/admin/applications/category/{category}', [ApplicationController::class, 'showCategory'])->name('applications.category');
    Route::get('/admin/applications/export/{category}', [ApplicationController::class, 'export'])->name('applications.export');
    Route::post('/admin/applications', [ApplicationController::class, 'store'])->name('applications.store');
    Route::put('/admin/applications/{id}', [ApplicationController::class, 'update'])->name('applications.update');
    Route::delete('/admin/applications/{id}', [ApplicationController::class, 'destroy'])->name('applications.destroy');
    Route::post('/admin/applications/{category}/{id}/approve', [ApplicationController::class, 'approveApplication'])->name('applications.approve');
    Route::post('/admin/applications/{category}/{id}/reject', [ApplicationController::class, 'rejectApplication'])->name('applications.reject');

    // Projects routes
    Route::get('/admin/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/admin/projects/category/{category}', [ProjectController::class, 'showCategory'])->name('projects.category');
    Route::get('/admin/projects/export/{category}', [ProjectController::class, 'export'])->name('projects.export');
    Route::post('/admin/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::put('/admin/projects/{id}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/admin/projects/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::get('/admin/projects/{id}', [ProjectController::class, 'show'])->name('projects.show');
    Route::post('/admin/projects/{id}/approve', [ProjectController::class, 'approveStage'])->name('projects.approve');
    Route::post('/admin/projects/{id}/assign-application', [ProjectController::class, 'assignApplication'])->name('projects.assign_application');
    Route::post('/admin/projects/{id}/upload-file', [ProjectController::class, 'uploadFile'])->name('projects.upload_file');
    Route::post('/admin/projects/{id}/materials', [ProjectController::class, 'addMaterial'])->name('projects.add_material');
    Route::put('/admin/projects/{id}/materials/{index}', [ProjectController::class, 'updateMaterial'])->name('projects.update_material');
    Route::delete('/admin/projects/{id}/materials/{index}', [ProjectController::class, 'deleteMaterial'])->name('projects.delete_material');

    Route::post('/admin/projects/{id}/expenses', [ProjectController::class, 'addExpense'])->name('projects.add_expense');
    Route::put('/admin/projects/{id}/expenses/{index}', [ProjectController::class, 'updateExpense'])->name('projects.update_expense');
    Route::delete('/admin/projects/{id}/expenses/{index}', [ProjectController::class, 'deleteExpense'])->name('projects.delete_expense');

    Route::post('/admin/projects/{id}/upload-photo', [ProjectController::class, 'uploadPhoto'])->name('projects.upload_photo');
    Route::delete('/admin/projects/{id}/delete-photo/{index}', [ProjectController::class, 'deletePhoto'])->name('projects.delete_photo');
    Route::post('/admin/projects/{id}/completion-details', [ProjectController::class, 'saveCompletionDetails'])->name('projects.save_completion_details');
});
