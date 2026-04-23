<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ExaminationResultController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RoleMiddleware;

// Resource routes cho patient
Route::post('user/login', [UserController::class, 'login']);
Route::post('patient/login', [PatientController::class, 'login']);
Route::post('patient', [PatientController::class, 'store'])->name('patient.store');
Route::get('/login', function () {
    return response()->json(['message' => 'Please login'], 401);
})->name('login');

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/doctors', [UserController::class, 'getDoctor']);
    
    Route::prefix('appointments')->group(function () {
        Route::post('/new', [AppointmentController::class, 'store']);

        Route::get('/doctor/{doctor}', [AppointmentController::class, 'getByDoctorAndDate']);
        Route::get('/history/{id}', [AppointmentController::class, 'historyByUser']);

        Route::get('/total', [AppointmentController::class, 'total'])->middleware(RoleMiddleware::class . ':1-2-3');
        Route::get('/success', [AppointmentController::class, 'successAppointments'])->middleware(RoleMiddleware::class . ':1-2-3');
        Route::get('/failed', [AppointmentController::class, 'failed'])->middleware(RoleMiddleware::class . ':1-2-3');

        Route::get('/topSymptoms', [AppointmentController::class, 'topSymptoms']);
        Route::get('/topDoctors', [AppointmentController::class, 'topDoctors']);

        Route::get('/getDoctorAppointmentStatsToday', [AppointmentController::class, 'getDoctorAppointmentStatsToday'])->middleware(RoleMiddleware::class . ':1');
        Route::get('/getTotalAppointmentsByRange', [AppointmentController::class, 'getTotalAppointmentsByRange'])->middleware(RoleMiddleware::class . ':1');

        Route::get('/today/{id}', [AppointmentController::class, 'getTodayAppointments'])->middleware(RoleMiddleware::class . ':1-2-3');
        Route::get('/{id?}/patient-info', [AppointmentController::class, 'getPatientAppointment']);
        Route::get('/staff', [AppointmentController::class, 'getAllPatientsWithStaff'])->middleware(RoleMiddleware::class . ':1-3');

        Route::put('/{id}/status', [AppointmentController::class, 'updateStatus'])->middleware(RoleMiddleware::class . ':1-3');
        Route::put('/{id}/doctor', [AppointmentController::class, 'updateDoctor'])->middleware(RoleMiddleware::class . ':1-3');

        Route::get('/doctor/{id}/history', [AppointmentController::class, 'getHistoryByDoctor'])->middleware(RoleMiddleware::class . ':1-2');
    });
    Route::get('patient', [PatientController::class, 'index'])->name('patient.index');
    Route::get('patient/{patient}', [PatientController::class, 'show'])->name('patient.show');
    Route::put('patient/{patient}', [PatientController::class, 'update'])->name('patient.update');
    
    Route::apiResource('users', UserController::class);
    Route::get('users/role/{id_role}', [UserController::class, 'getUsersByRole']);
    
    Route::apiResource('users', UserController::class);
    Route::prefix('examinations')->group(function () {
        Route::get('/{id}', [ExaminationResultController::class, 'show']);
    });
    Route::post('examinations/{id}/diagnosis', [ExaminationResultController::class, 'store'])->middleware(RoleMiddleware::class . ':1-2');
});
