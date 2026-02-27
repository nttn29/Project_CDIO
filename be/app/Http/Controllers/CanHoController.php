<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CanHoController extends Controller
{
    public function available()
    {
        return DB::table('can_ho')
            ->whereNull('id_cu_dan')
            ->select(['id_can_ho', 'so_can_ho', 'tang', 'id_toa_nha'])
            ->orderBy('id_toa_nha')
            ->orderBy('tang')
            ->orderBy('so_can_ho')
            ->get();
    }

    public function index()
    {
        return DB::table('can_ho')->get();
    }

    public function show($id)
    {
        return DB::table('can_ho')->where('id_can_ho', $id)->first();
    }

    public function store(Request $request)
    {
        $id = DB::table('can_ho')->insertGetId($request->only(['id_toa_nha','so_can_ho','tang','id_cu_dan']));
        return response()->json(['id' => $id], 201);
    }

    public function update(Request $request, $id)
    {
        DB::table('can_ho')->where('id_can_ho', $id)->update($request->except(['id_can_ho']));
        return response()->noContent();
    }

    public function destroy($id)
    {
        DB::table('can_ho')->where('id_can_ho', $id)->delete();
        return response()->noContent();
    }
}
