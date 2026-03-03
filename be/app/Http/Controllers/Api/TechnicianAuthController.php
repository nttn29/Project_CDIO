<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TechnicianAuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:nguoi_dung,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        $user = NguoiDung::create([
            'ten' => $data['name'],
            'email' => $data['email'],
            'dien_thoai' => $data['phone'],
            'mat_khau' => Hash::make($data['password']),
            'vai_tro' => 'nhan_vien',
            'trang_thai' => 'active',
        ]);

        return response()->json([
            'message' => 'Đăng ký thành công.',
            'data' => [
                'id' => $user->id_nguoi_dung,
                'ten' => $user->ten,
                'email' => $user->email,
                'dien_thoai' => $user->dien_thoai,
                'vai_tro' => $user->vai_tro,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = $data['identifier'];
        $query = NguoiDung::query()->whereIn('vai_tro', ['technician', 'nhan_vien']);

        if (str_contains($identifier, '@')) {
            $query->where('email', $identifier);
        } else {
            $query->where('dien_thoai', $identifier);
        }

        $user = $query->first();

        if (!$user || !Hash::check($data['password'], $user->mat_khau)) {
            throw ValidationException::withMessages([
                'identifier' => ['Sai tài khoản hoặc mật khẩu.'],
            ]);
        }

        return response()->json([
            'message' => 'Đăng nhập thành công.',
            'data' => [
                'id' => $user->id_nguoi_dung,
                'ten' => $user->ten,
                'email' => $user->email,
                'dien_thoai' => $user->dien_thoai,
                'vai_tro' => $user->vai_tro,
            ],
        ]);
    }
}
