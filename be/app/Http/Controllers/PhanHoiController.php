<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhanHoiController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('phan_hoi as p')
            ->leftJoin('nguoi_dung as nd', 'nd.id_nguoi_dung', '=', 'p.id_cu_dan')
            ->leftJoin('yeu_cau_bao_tri as y', 'y.id_yeu_cau', '=', 'p.id_yeu_cau')
            ->leftJoin('loai_su_co as l', 'l.id_loai_su_co', '=', 'y.id_loai_su_co')
            ->select(
                'p.*',
                'nd.ten as ten_cu_dan',
                'l.ten_loai as ten_loai_su_co'
            );

        if ($request->filled('id_cu_dan')) {
            $query->where('p.id_cu_dan', (int) $request->query('id_cu_dan'));
        }

        if ($request->filled('id_yeu_cau')) {
            $query->where('p.id_yeu_cau', (int) $request->query('id_yeu_cau'));
        }

        return $query
            ->orderByDesc('p.created_at')
            ->get()
            ->map(function ($row) {
                return [
                    'id_phan_hoi' => $row->id_phan_hoi,
                    'id_yeu_cau' => $row->id_yeu_cau,
                    'id_cu_dan' => $row->id_cu_dan,
                    'danh_gia' => (int) $row->danh_gia,
                    'binh_luan' => $row->binh_luan,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                    'cu_dan' => [
                        'ten' => $row->ten_cu_dan,
                    ],
                    'yeu_cau' => [
                        'id_yeu_cau' => $row->id_yeu_cau,
                        'loai_su_co' => [
                            'ten_loai' => $row->ten_loai_su_co,
                        ],
                    ],
                ];
            });
    }

    public function show($id)
    {
        return DB::table('phan_hoi')->where('id_phan_hoi', $id)->first();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_yeu_cau' => 'required|integer|exists:yeu_cau_bao_tri,id_yeu_cau',
            'id_cu_dan' => 'required|integer|exists:nguoi_dung,id_nguoi_dung',
            'danh_gia' => 'required|integer|min:1|max:5',
            'binh_luan' => 'nullable|string',
        ]);

        $ownedCompletedRequest = DB::table('yeu_cau_bao_tri')
            ->where('id_yeu_cau', $data['id_yeu_cau'])
            ->where('id_cu_dan', $data['id_cu_dan'])
            ->where('trang_thai', 'hoan_thanh')
            ->exists();

        if (!$ownedCompletedRequest) {
            return response()->json([
                'error' => 'Chi co the danh gia yeu cau da hoan thanh cua ban.',
            ], 422);
        }

        $existing = DB::table('phan_hoi')
            ->where('id_yeu_cau', $data['id_yeu_cau'])
            ->where('id_cu_dan', $data['id_cu_dan'])
            ->first();

        if ($existing) {
            return response()->json([
                'error' => 'Review exists',
                'existing_id' => $existing->id_phan_hoi,
            ], 409);
        }

        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = DB::table('phan_hoi')->insertGetId($data);
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
