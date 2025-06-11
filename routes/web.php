<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KioskController;

// Authentication Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register.submit');

// Protected Routes
Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
      // User Management (Super Admin only)
    Route::middleware('superadmin')->prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
    
    // Admin Management (Super Admin only)
    Route::middleware('superadmin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/staff/export', [AdminController::class, 'exportStaffData'])->name('admin.staff.export');
        Route::post('/maintenance', [AdminController::class, 'systemMaintenance'])->name('admin.maintenance');
        Route::post('/backup', [AdminController::class, 'createBackup'])->name('admin.backup');
        Route::get('/logs', [AdminController::class, 'getLogs'])->name('admin.logs');
        Route::get('/analytics', [AdminController::class, 'getAnalytics'])->name('admin.analytics');
    });
    
    // Profile Management
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [UserController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/image', [UserController::class, 'updateImage'])->name('profile.image');
    
    // Patient Management
    Route::prefix('patients')->group(function () {
        Route::get('/', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/create', [PatientController::class, 'create'])->name('patients.create');
        Route::post('/', [PatientController::class, 'store'])->name('patients.store');
        Route::get('/{patient}', [PatientController::class, 'show'])->name('patients.show');
        Route::get('/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
        Route::put('/{patient}', [PatientController::class, 'update'])->name('patients.update');
        Route::delete('/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
        Route::get('/{patient}/medical-records', [PatientController::class, 'medicalRecords'])->name('patients.medical-records');
        Route::post('/{patient}/medical-records', [PatientController::class, 'storeMedicalRecord'])->name('patients.medical-records.store');
    });
    
    // Queue Management
    Route::prefix('queue')->group(function () {
        Route::get('/', [QueueController::class, 'index'])->name('queue.index');
        Route::get('/manage', [QueueController::class, 'manage'])->name('queue.manage');
        Route::post('/call-next', [QueueController::class, 'callNext'])->name('queue.call-next');
        Route::post('/complete/{queue}', [QueueController::class, 'complete'])->name('queue.complete');
        Route::post('/skip/{queue}', [QueueController::class, 'skip'])->name('queue.skip');
        Route::get('/display', [QueueController::class, 'display'])->name('queue.display');
    });
      // Appointment Management
    Route::prefix('appointments')->group(function () {
        Route::get('/', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/check-availability', [AppointmentController::class, 'checkAvailability'])->name('appointments.check-availability');
        Route::get('/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
        Route::put('/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
        Route::delete('/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    });
});

// Public Kiosk Routes
Route::prefix('kiosk')->group(function () {
    Route::get('/', [KioskController::class, 'index'])->name('kiosk.index');
    Route::post('/register', [KioskController::class, 'register'])->name('kiosk.register');
    Route::get('/queue-number/{queueNumber}', [KioskController::class, 'printQueueNumber'])->name('kiosk.print');
});

// Mobile Patient Routes
Route::prefix('mobile')->group(function () {
    Route::get('/', [KioskController::class, 'mobileIndex'])->name('mobile.index');
    Route::get('/queue-status', [KioskController::class, 'queueStatus'])->name('mobile.queue-status');
});
