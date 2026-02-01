<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoaiSuCoController extends Controller
{
    public function index()
    {
        return DB::table('loai_su_co')->get();
    }

    public function show($id)
    {
        return DB::table('loai_su_co')->where('id_loai_su_co', $id)->first();
    }

    public function store(Request $request)
    {
        $id = DB::table('loai_su_co')->insertGetId($request->only(['ten_loai','muc_uu_tien','mo_ta']));
        return response()->json(['id' => $id], 201);
    }

    public function update(Request $request, $id)
    {
        DB::table('loai_su_co')->where('id_loai_su_co', $id)->update($request->except(['id_loai_su_co']));
        return response()->noContent();
    }

    public function destroy($id)
    {
        DB::table('loai_su_co')->where('id_loai_su_co', $id)->delete();
        return response()->noContent();
    }
}
