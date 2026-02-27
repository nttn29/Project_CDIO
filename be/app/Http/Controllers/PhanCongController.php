<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhanCongController extends Controller
{
    public function index()
    {
        return DB::table('phan_cong')->get();
    }

    public function show($id)
    {
        return DB::table('phan_cong')->where('id_phan_cong', $id)->first();
    }

    public function store(Request $request)
    {
        $id = DB::table('phan_cong')->insertGetId($request->only(['id_yeu_cau','id_ky_thuat_vien','ngay_phan_cong','gio_hen','trang_thai']));
        return response()->json(['id' => $id], 201);
    }

    public function update(Request $request, $id)
    {
        DB::table('phan_cong')->where('id_phan_cong', $id)->update($request->except(['id_phan_cong']));
        return response()->noContent();
    }

    public function destroy($id)
    {
        DB::table('phan_cong')->where('id_phan_cong', $id)->delete();
        return response()->noContent();
    }
}
