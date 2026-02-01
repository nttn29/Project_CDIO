<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ToaNhaController extends Controller
{
    public function index()
    {
        return DB::table('toa_nha')->get();
    }

    public function show($id)
    {
        return DB::table('toa_nha')->where('id_toa_nha', $id)->first();
    }

    public function store(Request $request)
    {
        $id = DB::table('toa_nha')->insertGetId($request->only(['ten_toa_nha','dia_chi','mo_ta']));
        return response()->json(['id' => $id], 201);
    }

    public function update(Request $request, $id)
    {
        DB::table('toa_nha')->where('id_toa_nha', $id)->update($request->except(['id_toa_nha']));
        return response()->noContent();
    }

    public function destroy($id)
    {
        DB::table('toa_nha')->where('id_toa_nha', $id)->delete();
        return response()->noContent();
    }
}
