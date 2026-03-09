<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * TechnicianAuthController – Xác thực dành riêng cho ứng dụng kỹ thuật viên.
 *
 * Cung cấp hai endpoint:
 *   POST /api/technician/register – Đăng ký tài khoản kỹ thuật viên mới.
 *   POST /api/technician/login    – Đăng nhập bằng email hoặc số điện thoại.
 *
 * Kỹ thuật viên được lưu trong bảng nguoi_dung với vai_tro = 'nhan_vien'.
 */
class TechnicianAuthController extends Controller
{
    /**
     * Đăng ký tài khoản kỹ thuật viên mới.
     *
     * Tạo bản ghi trong bảng nguoi_dung với vai_tro = 'nhan_vien'
     * và trả về thông tin cơ bản của tài khoản vừa tạo (HTTP 201).
     *
     * @param Request $request Dữ liệu đầu vào: name, email, phone, password
     */
    public function register(Request $request)
    {
        // Validate dữ liệu đầu vào: tên, email duy nhất, sđt, mật khẩu tối thiểu 6 ký tự
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:nguoi_dung,email',
            'phone'    => 'required|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        // Tạo bản ghi người dùng mới, mật khẩu được hash bằng bcrypt
        $user = NguoiDung::create([
            'ten'        => $data['name'],
            'email'      => $data['email'],
            'dien_thoai' => $data['phone'],
            'mat_khau'   => Hash::make($data['password']),
            'vai_tro'    => 'nhan_vien', // vai trò mặc định cho kỹ thuật viên
            'trang_thai' => 'active',
        ]);

        return response()->json([
            'message' => 'Đăng ký thành công.',
            'data' => [
                'id'         => $user->id_nguoi_dung,
                'ten'        => $user->ten,
                'email'      => $user->email,
                'dien_thoai' => $user->dien_thoai,
                'vai_tro'    => $user->vai_tro,
            ],
        ], 201);
    }

    /**
     * Đăng nhập cho kỹ thuật viên.
     *
     * Cho phép đăng nhập bằng email hoặc số điện thoại.
     * Chỉ tài khoản có vai_tro là 'technician' hoặc 'nhan_vien' mới được phép đăng nhập.
     * Nếu sai tài khoản/mật khẩu sẽ trả về lỗi ValidationException.
     *
     * @param Request $request Dữ liệu đầu vào: identifier (email hoặc sđt), password
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'identifier' => 'required|string',  // email hoặc số điện thoại
            'password'   => 'required|string',
        ]);

        $identifier = $data['identifier'];

        // Chỉ tìm trong các tài khoản có vai trò kỹ thuật viên
        $query = NguoiDung::query()->whereIn('vai_tro', ['technician', 'nhan_vien']);

        // Phân biệt đăng nhập bằng email hay số điện thoại
        if (str_contains($identifier, '@')) {
            $query->where('email', $identifier);
        } else {
            $query->where('dien_thoai', $identifier);
        }

        $user = $query->first();

        // Kiểm tra tài khoản tồn tại và mật khẩu đúng
        if (!$user || !Hash::check($data['password'], $user->mat_khau)) {
            throw ValidationException::withMessages([
                'identifier' => ['Sai tài khoản hoặc mật khẩu.'],
            ]);
        }

        return response()->json([
            'message' => 'Đăng nhập thành công.',
            'data' => [
                'id'         => $user->id_nguoi_dung,
                'ten'        => $user->ten,
                'email'      => $user->email,
                'dien_thoai' => $user->dien_thoai,
                'vai_tro'    => $user->vai_tro,
            ],
        ]);
    }
}
