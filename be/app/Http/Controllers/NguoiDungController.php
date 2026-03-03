<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NguoiDungController extends Controller
{
    public function index()
    {
        return DB::table('nguoi_dung')
            ->leftJoin('can_ho', 'can_ho.id_cu_dan', '=', 'nguoi_dung.id_nguoi_dung')
            ->select('nguoi_dung.*', 'can_ho.so_can_ho')
            ->get();
    }

    public function show($id)
    {
        return DB::table('nguoi_dung')->where('id_nguoi_dung', $id)->first();
    }

    public function store(Request $request)
    {
        $id = DB::table('nguoi_dung')->insertGetId($request->only(['email','ten','mat_khau','dien_thoai','vai_tro','trang_thai']));
        return response()->json(['id' => $id], 201);
    }

    public function update(Request $request, $id)
    {
        DB::table('nguoi_dung')->where('id_nguoi_dung', $id)->update($request->except(['id_nguoi_dung']));
        return response()->noContent();
    }

    public function destroy($id)
    {
        DB::table('nguoi_dung')->where('id_nguoi_dung', $id)->delete();
        return response()->noContent();
    }

    public function technicians()
    {
        return DB::table('nguoi_dung')
            ->whereIn('vai_tro', ['nhan_vien', 'technician', 'ky_thuat_vien'])
            ->where(function ($q) {
                $q->whereNull('trang_thai')
                    ->orWhere('trang_thai', 'active');
            })
            ->select('id_nguoi_dung', 'ten', 'email', 'dien_thoai', 'vai_tro', 'trang_thai')
            ->orderBy('ten')
            ->get();
    }
}
