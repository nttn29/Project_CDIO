<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ============ PUBLIC ROUTES ============
// Authentication
Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);

// Apartments
Route::get('can-ho/available', [\App\Http\Controllers\CanHoController::class, 'available']);

// ============ RESIDENT ROUTES ============
// Note: Frontend (Resident) currently uses a "fake-token" login stub.
// To keep FE working in dev, Resident endpoints are not protected here.
// You can re-add `auth:sanctum` once FE is wired to real auth.
Route::group([], function () {
    // Technician directory for admin assignment
    Route::get('technicians', [\App\Http\Controllers\NguoiDungController::class, 'technicians']);
    Route::get('ky-thuat-vien', [\App\Http\Controllers\NguoiDungController::class, 'technicians']);

    // Issue types (CRUD)
    Route::get('loai-su-co', [\App\Http\Controllers\LoaiSuCoController::class, 'index']);
    Route::post('loai-su-co', [\App\Http\Controllers\LoaiSuCoController::class, 'store']);
    Route::put('loai-su-co/{id}', [\App\Http\Controllers\LoaiSuCoController::class, 'update']);
    Route::delete('loai-su-co/{id}', [\App\Http\Controllers\LoaiSuCoController::class, 'destroy']);
    
    // Backward-compatible alias (snake_case)
    Route::get('loai_su_co', [\App\Http\Controllers\LoaiSuCoController::class, 'index']);

    // User profile management
    Route::get('users/{id}', [\App\Http\Controllers\NguoiDungController::class, 'show']);
    Route::put('users/{id}', [\App\Http\Controllers\NguoiDungController::class, 'update']);

    // My maintenance requests
    Route::get('users/{id}/requests', [\App\Http\Controllers\NguoiDungController::class, 'myRequests']);
    Route::get('users/{id}/notifications', [\App\Http\Controllers\NguoiDungController::class, 'notifications']);

    // Maintenance requests - FE expects /yeu-cau-bao-tri (kebab-case)
    Route::get('yeu-cau-bao-tri', [\App\Http\Controllers\YeuCauBaoTriController::class, 'index']);
    Route::post('yeu-cau-bao-tri', [\App\Http\Controllers\YeuCauBaoTriController::class, 'store']);
    Route::get('yeu-cau-bao-tri/{id}', [\App\Http\Controllers\YeuCauBaoTriController::class, 'show']);
    Route::put('yeu-cau-bao-tri/{id}', [\App\Http\Controllers\YeuCauBaoTriController::class, 'update']);
    Route::delete('yeu-cau-bao-tri/{id}', [\App\Http\Controllers\YeuCauBaoTriController::class, 'destroy']);
    Route::post('yeu-cau-bao-tri/{id}/confirm', [\App\Http\Controllers\YeuCauBaoTriController::class, 'confirm']);
    Route::post('yeu-cau-bao-tri/{id}/upload-image', [\App\Http\Controllers\YeuCauBaoTriController::class, 'uploadImage']);
    Route::post('yeu-cau-bao-tri/{id}/status', [\App\Http\Controllers\YeuCauBaoTriController::class, 'changeStatus']);

    // Backward-compatible aliases (snake_case)
    Route::post('yeu_cau', [\App\Http\Controllers\YeuCauBaoTriController::class, 'store']);
    Route::get('yeu_cau', [\App\Http\Controllers\YeuCauBaoTriController::class, 'index']);
    Route::get('yeu_cau/{id}', [\App\Http\Controllers\YeuCauBaoTriController::class, 'show']);
    Route::put('yeu_cau/{id}', [\App\Http\Controllers\YeuCauBaoTriController::class, 'update']);
    Route::delete('yeu_cau/{id}', [\App\Http\Controllers\YeuCauBaoTriController::class, 'destroy']);
    Route::post('yeu_cau/{id}/confirm', [\App\Http\Controllers\YeuCauBaoTriController::class, 'confirm']);
    Route::post('yeu_cau/{id}/upload-image', [\App\Http\Controllers\YeuCauBaoTriController::class, 'uploadImage']);
    Route::post('yeu_cau/{id}/status', [\App\Http\Controllers\YeuCauBaoTriController::class, 'changeStatus']);

    // Assignment endpoints for admin UI (dev mode without Sanctum)
    Route::get('phan-cong', [\App\Http\Controllers\PhanCongController::class, 'index']);
    Route::post('phan-cong', [\App\Http\Controllers\PhanCongController::class, 'store']);

    // Images - FE expects /hinh-anh/{id}
    Route::delete('hinh-anh/{id}', [\App\Http\Controllers\YeuCauBaoTriController::class, 'deleteImage']);
    // Backward-compatible alias
    Route::delete('hinh_anh/{id}', [\App\Http\Controllers\YeuCauBaoTriController::class, 'deleteImage']);

    // Feedback and rating (FE uses snake_case `phan_hoi`)
    Route::post('phan_hoi', [\App\Http\Controllers\PhanHoiController::class, 'store']);
    Route::get('phan_hoi', [\App\Http\Controllers\PhanHoiController::class, 'index']);
    Route::get('phan_hoi/{id}', [\App\Http\Controllers\PhanHoiController::class, 'show']);
    Route::put('phan_hoi/{id}', [\App\Http\Controllers\PhanHoiController::class, 'update']);
    Route::delete('phan_hoi/{id}', [\App\Http\Controllers\PhanHoiController::class, 'destroy']);
    Route::get('phan_hoi/rating/average', [\App\Http\Controllers\PhanHoiController::class, 'getAverageRating']);
    Route::get('resident/{id}/rating', [\App\Http\Controllers\PhanHoiController::class, 'getResidentRating']);
});

// ============ TECHNICIAN ROUTES ============
Route::prefix('technician')->group(function () {
    Route::post('register', [\App\Http\Controllers\Api\TechnicianAuthController::class, 'register']);
    Route::post('login', [\App\Http\Controllers\Api\TechnicianAuthController::class, 'login']);
    Route::get('jobs/stats', [\App\Http\Controllers\Api\TechnicianJobController::class, 'stats']);
    Route::get('jobs', [\App\Http\Controllers\Api\TechnicianJobController::class, 'index']);
    Route::post('jobs', [\App\Http\Controllers\Api\TechnicianJobController::class, 'store']);
    Route::get('jobs/code/{code}', [\App\Http\Controllers\Api\TechnicianJobController::class, 'showByCode']);
    Route::get('jobs/{job}', [\App\Http\Controllers\Api\TechnicianJobController::class, 'show']);
    Route::patch('jobs/{job}', [\App\Http\Controllers\Api\TechnicianJobController::class, 'update']);
    Route::delete('jobs/{job}', [\App\Http\Controllers\Api\TechnicianJobController::class, 'destroy']);
});

// ============ STAFF ROUTES (dev mode - no auth) ============
// Note: Add auth middleware when Sanctum is properly installed
Route::get('yeu_cau', [\App\Http\Controllers\YeuCauBaoTriController::class, 'index']);
Route::post('yeu_cau/{id}/status', [\App\Http\Controllers\YeuCauBaoTriController::class, 'changeStatus']);
Route::get('phan_cong', [\App\Http\Controllers\PhanCongController::class, 'index']);
Route::post('phan_cong', [\App\Http\Controllers\PhanCongController::class, 'store']);
Route::post('phan_cong/{id}/complete', [\App\Http\Controllers\PhanCongController::class, 'complete']);
Route::get('nhat_ky', [\App\Http\Controllers\NhatKyCongViecController::class, 'index']);

// ============ ADMIN ROUTES ============
// Note: Add auth middleware when Sanctum is properly installed
// Users routes already added in the open group above
