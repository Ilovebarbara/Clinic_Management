<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\QueueController;
use App\Http\Controllers\Api\MedicalRecordController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public API routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/patient-login', [AuthController::class, 'patientLogin']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Queue status (public for kiosk displays)
Route::get('/queue/status', [QueueController::class, 'index']);
Route::get('/queue/currently-serving', [QueueController::class, 'currentlyServing']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Authentication
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    
    // Patients API
    Route::apiResource('patients', PatientController::class);
    Route::get('/patients/{patient}/medical-history', [PatientController::class, 'getMedicalHistory']);
    
    // Appointments API
    Route::apiResource('appointments', AppointmentController::class);
    Route::prefix('appointments')->group(function () {
        Route::get('today', [AppointmentController::class, 'today']);
        Route::get('upcoming', [AppointmentController::class, 'upcoming']);
        Route::get('{doctor_id}/available-slots', [AppointmentController::class, 'availableSlots']);
        Route::patch('{id}/confirm', [AppointmentController::class, 'confirm']);
        Route::patch('{id}/cancel', [AppointmentController::class, 'cancel']);
    });
    
    // Queue Management API
    Route::apiResource('queue', QueueController::class);
    Route::prefix('queue')->group(function () {
        Route::post('call-next', [QueueController::class, 'callNext']);
        Route::get('statistics', [QueueController::class, 'statistics']);
        Route::get('{id}/position', [QueueController::class, 'position']);
        Route::patch('{id}/cancel', [QueueController::class, 'cancel']);
        Route::post('reset', [QueueController::class, 'reset']);
    });
    
    // Medical Records API
    Route::apiResource('medical-records', MedicalRecordController::class);
    Route::prefix('medical-records')->group(function () {
        Route::get('patient/{patient_id}/history', [MedicalRecordController::class, 'patientHistory']);
        Route::get('patient/{patient_id}/vitals-trend', [MedicalRecordController::class, 'vitalsTrend']);
        Route::get('diagnosis-stats', [MedicalRecordController::class, 'diagnosisStats']);
        Route::get('search-diagnosis', [MedicalRecordController::class, 'searchByDiagnosis']);
        Route::get('template', [MedicalRecordController::class, 'template']);
    });
    
    // Dashboard stats
    Route::get('/dashboard/stats', function () {
        return response()->json([
            'patients_today' => \App\Models\Patient::whereDate('created_at', today())->count(),
            'appointments_today' => \App\Models\Appointment::whereDate('appointment_date', today())->count(),
            'queue_waiting' => \App\Models\QueueTicket::where('status', 'waiting')->count(),
            'queue_serving' => \App\Models\QueueTicket::where('status', 'serving')->count(),
        ]);
    });
});

// Mobile app specific routes
Route::prefix('mobile')->group(function () {
    Route::post('register', [AuthController::class, 'registerPatient']);
    Route::post('login', [AuthController::class, 'loginPatient']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('profile', [PatientController::class, 'profile']);
        Route::put('profile', [PatientController::class, 'updateProfile']);
        Route::get('appointments', [PatientController::class, 'appointments']);
        Route::get('medical-records', [PatientController::class, 'medicalRecords']);
        
        // Mobile-specific appointment routes
        Route::post('appointments', [AppointmentController::class, 'store']);
        Route::patch('appointments/{id}/cancel', [AppointmentController::class, 'cancel']);
        
        // Mobile queue management
        Route::post('queue/join', [QueueController::class, 'store']);
        Route::get('queue/{id}/status', [QueueController::class, 'position']);
    });
});

// Kiosk specific routes
Route::prefix('kiosk')->group(function () {
    Route::post('patient/search', [PatientController::class, 'search']);
    Route::post('patient/register', [PatientController::class, 'quickRegister']);
    Route::post('queue/generate', [QueueController::class, 'store']);
    Route::get('queue/status', [QueueController::class, 'index']);
    Route::get('queue/currently-serving', [QueueController::class, 'currentlyServing']);
    Route::get('appointments/available-slots', [AppointmentController::class, 'availableSlots']);
});

// Admin specific routes
Route::prefix('admin')->middleware(['auth:sanctum', 'can:system-admin'])->group(function () {
    Route::get('/users', [AuthController::class, 'getUsers']);
    Route::post('/users', [AuthController::class, 'createUser']);
    Route::put('/users/{user}', [AuthController::class, 'updateUser']);
    Route::delete('/users/{user}', [AuthController::class, 'deleteUser']);
    
    // Enhanced admin functionality
    Route::get('/staff/export', [AdminController::class, 'exportStaffData']);
    Route::post('/maintenance', [AdminController::class, 'systemMaintenance']);
    Route::post('/backup', [AdminController::class, 'createBackup']);
    Route::get('/logs', [AdminController::class, 'getLogs']);
    Route::get('/analytics', [AdminController::class, 'getAnalytics']);
    
    Route::get('/reports/daily', function () {
        return response()->json(['message' => 'Daily report functionality to be implemented']);
    });
    
    Route::get('/reports/monthly', function () {
        return response()->json(['message' => 'Monthly report functionality to be implemented']);
    });
    
    Route::get('/system/backup', function () {
        return response()->json(['message' => 'System backup functionality to be implemented']);
    });
});
