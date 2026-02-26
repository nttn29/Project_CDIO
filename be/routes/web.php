<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TechnicianAuthController;
use App\Http\Controllers\Api\TechnicianJobController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api/technician')->group(function () {
    Route::post('/register', [TechnicianAuthController::class, 'register']);
    Route::post('/login', [TechnicianAuthController::class, 'login']);
    Route::get('/jobs', [TechnicianJobController::class, 'index']);
    Route::post('/jobs', [TechnicianJobController::class, 'store']);
    Route::get('/jobs/code/{code}', [TechnicianJobController::class, 'showByCode']);
    Route::get('/jobs/{job}', [TechnicianJobController::class, 'show']);
    Route::patch('/jobs/{job}', [TechnicianJobController::class, 'update']);
    Route::delete('/jobs/{job}', [TechnicianJobController::class, 'destroy']);
});
