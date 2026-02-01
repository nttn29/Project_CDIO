<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YeuCauBaoTriController extends Controller
{
    public function index()
    {
        return DB::table('yeu_cau_bao_tri')->get();
    }

    public function show($id)
    {
        return DB::table('yeu_cau_bao_tri')->where('id_yeu_cau', $id)->first();
    }

    public function store(Request $request)
    {
        $id = DB::table('yeu_cau_bao_tri')->insertGetId($request->only(['id_cu_dan','id_can_ho','id_loai_su_co','mo_ta','thoi_gian_uu_tien','trang_thai']));
        return response()->json(['id' => $id], 201);
    }

    public function update(Request $request, $id)
    {
        DB::table('yeu_cau_bao_tri')->where('id_yeu_cau', $id)->update($request->except(['id_yeu_cau']));
        return response()->noContent();
    }

    public function destroy($id)
    {
        DB::table('yeu_cau_bao_tri')->where('id_yeu_cau', $id)->delete();
        return response()->noContent();
    }
}
