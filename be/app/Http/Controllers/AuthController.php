<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\NguoiDung;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Handle user registration
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->only(['email', 'ten', 'mat_khau', 'dien_thoai', 'vai_tro']);

        $user = NguoiDung::create($data);

        return response()->json(['success' => true, 'user' => $user], 201);
    }
}
