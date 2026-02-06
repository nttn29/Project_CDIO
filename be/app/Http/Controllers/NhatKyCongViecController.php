<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NhatKyCongViecController extends Controller
{
    public function index()
    {
        return DB::table('nhat_ky_cong_viec')->get();
    }

    public function show($id)
    {
        return DB::table('nhat_ky_cong_viec')->where('id_nhat_ky', $id)->first();
    }

    public function store(Request $request)
    {
        $id = DB::table('nhat_ky_cong_viec')->insertGetId($request->only(['id_phan_cong','mo_ta_cong_viec','so_gio_lam','chi_phi','ngay_nhat_ky']));
        return response()->json(['id' => $id], 201);
    }

    public function update(Request $request, $id)
    {
        DB::table('nhat_ky_cong_viec')->where('id_nhat_ky', $id)->update($request->except(['id_nhat_ky']));
        return response()->noContent();
    }

    public function destroy($id)
    {
        DB::table('nhat_ky_cong_viec')->where('id_nhat_ky', $id)->delete();
        return response()->noContent();
    }
}
