<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhanHoiController extends Controller
{
    public function index()
    {
        return DB::table('phan_hoi')->get();
    }

    public function show($id)
    {
        return DB::table('phan_hoi')->where('id_phan_hoi', $id)->first();
    }

    public function store(Request $request)
    {
        $id = DB::table('phan_hoi')->insertGetId($request->only(['id_yeu_cau','id_cu_dan','danh_gia','binh_luan']));
        return response()->json(['id' => $id], 201);
    }

    public function update(Request $request, $id)
    {
        DB::table('phan_hoi')->where('id_phan_hoi', $id)->update($request->except(['id_phan_hoi']));
        return response()->noContent();
    }

    public function destroy($id)
    {
        DB::table('phan_hoi')->where('id_phan_hoi', $id)->delete();
        return response()->noContent();
    }
}
