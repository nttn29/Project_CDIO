<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\NguoiDung;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle user registration
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->only(['email', 'ten', 'mat_khau', 'dien_thoai', 'vai_tro']);
        $data['vai_tro'] = $data['vai_tro'] ?? 'cu_dan';
        $idCanHo = $request->input('id_can_ho');

        $user = DB::transaction(function () use ($data, $idCanHo) {
            $newUser = NguoiDung::create($data);

            $updated = DB::table('can_ho')
                ->where('id_can_ho', $idCanHo)
                ->whereNull('id_cu_dan')
                ->update(['id_cu_dan' => $newUser->id_nguoi_dung]);

            if (!$updated) {
                throw ValidationException::withMessages([
                    'id_can_ho' => ['Can ho da duoc gan cho cu dan khac'],
                ]);
            }

            return $newUser;
        });

        $user->load('canHo');

        return response()->json(['success' => true, 'user' => $user], 201);
    }

    /**
     * Handle user login
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'mat_khau' => ['required', 'string'],
        ]);

        $user = NguoiDung::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['mat_khau'], $user->mat_khau)) {
            return response()->json([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không đúng',
            ], 401);
        }

        $user->load('canHo');

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }
}
