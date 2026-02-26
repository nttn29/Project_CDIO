<?php

namespace App\Http\Controllers;

use App\Models\KyThuatVien;

class KyThuatVienController extends Controller
{
  
public function index()
{
    try {
        return \App\Models\KyThuatVien::with('yeuCauBt.chuCanHo')->get();
    } catch (\Throwable $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}

    
}
