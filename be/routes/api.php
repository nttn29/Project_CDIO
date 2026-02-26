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
    // Issue types (FE expects kebab-case)
    Route::get('loai-su-co', [\App\Http\Controllers\LoaiSuCoController::class, 'index']);
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

    // Backward-compatible aliases (snake_case)
    Route::post('yeu_cau', [\App\Http\Controllers\YeuCauBaoTriController::class, 'store']);
    Route::get('yeu_cau', [\App\Http\Controllers\YeuCauBaoTriController::class, 'index']);
    Route::get('yeu_cau/{id}', [\App\Http\Controllers\YeuCauBaoTriController::class, 'show']);
    Route::put('yeu_cau/{id}', [\App\Http\Controllers\YeuCauBaoTriController::class, 'update']);
    Route::delete('yeu_cau/{id}', [\App\Http\Controllers\YeuCauBaoTriController::class, 'destroy']);
    Route::post('yeu_cau/{id}/confirm', [\App\Http\Controllers\YeuCauBaoTriController::class, 'confirm']);
    Route::post('yeu_cau/{id}/upload-image', [\App\Http\Controllers\YeuCauBaoTriController::class, 'uploadImage']);

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

// ============ STAFF ROUTES ============
Route::middleware('auth:sanctum')->group(function () {
    // Get all maintenance requests (staff)
    Route::get('yeu_cau', [\App\Http\Controllers\YeuCauBaoTriController::class, 'index']);

    // Status management
    Route::post('yeu_cau/{id}/status', [\App\Http\Controllers\YeuCauBaoTriController::class, 'changeStatus']);

    // Assignments and work logs
    Route::get('phan_cong', [\App\Http\Controllers\PhanCongController::class, 'index']);
    Route::post('phan_cong', [\App\Http\Controllers\PhanCongController::class, 'store']);
    Route::post('phan_cong/{id}/complete', [\App\Http\Controllers\PhanCongController::class, 'complete']);

    Route::get('nhat_ky', [\App\Http\Controllers\NhatKyCongViecController::class, 'index']);
});
 // ====== CHỦ CĂN HỘ ======
    Route::get('chu-can-ho', [\App\Http\Controllers\Chu_can_hoController::class, 'index']);
    Route::post('chu-can-ho', [\App\Http\Controllers\Chu_can_hoController::class, 'store']);
    Route::get('chu-can-ho/{id}', [\App\Http\Controllers\Chu_can_hoController::class, 'show']);
    Route::put('chu-can-ho/{id}', [\App\Http\Controllers\Chu_can_hoController::class, 'update']);
    Route::delete('chu-can-ho/{id}', [\App\Http\Controllers\Chu_can_hoController::class, 'destroy']);
// sodo nha
Route::get('so-do-toa-nha', [\App\Http\Controllers\Chu_can_hoController::class, 'soDo']);
//Yeu cầu bảo trì===
use App\Http\Controllers\YeuCauBtController;

Route::get('yeu-cau-bt', [YeuCauBtController::class, 'index']);
Route::post('yeu-cau-bt', [YeuCauBtController::class, 'store']);
Route::put('yeu-cau-bt/{id}', [YeuCauBtController::class, 'updateStatus']);
Route::delete('yeu-cau-bt/{id}', [YeuCauBtController::class, 'destroy']);
Route::put('yeu-cau-bt/{id}', [YeuCauBtController::class, 'update']);
Route::get('/yeu-cau-bt/{id}', [YeuCauBtController::class, 'show']);
Route::get('/hoa-don', [YeuCauBtController::class, 'invoices']);
//Ky thuật viên
use App\Http\Controllers\KyThuatVienController;
Route::get('ky-thuat-vien', [KyThuatVienController::class, 'index']);
// ============ ADMIN ROUTES ============
Route::middleware('auth:sanctum')->group(function () {
    Route::get('users', [\App\Http\Controllers\NguoiDungController::class, 'index']);
    Route::post('users', [\App\Http\Controllers\NguoiDungController::class, 'store']);
    Route::delete('users/{id}', [\App\Http\Controllers\NguoiDungController::class, 'destroy']);
   });
 