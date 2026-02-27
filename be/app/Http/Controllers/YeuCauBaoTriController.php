<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YeuCauBaoTriController extends Controller
{
    public function index(Request $request)
    {
        $latestAssignment = DB::table('phan_cong as p1')
            ->select('p1.id_yeu_cau', DB::raw('MAX(p1.id_phan_cong) as latest_id_phan_cong'))
            ->groupBy('p1.id_yeu_cau');

        $query = DB::table('yeu_cau_bao_tri as y')
            ->leftJoin('loai_su_co as l', 'l.id_loai_su_co', '=', 'y.id_loai_su_co')
            ->leftJoin('nguoi_dung as nd', 'nd.id_nguoi_dung', '=', 'y.id_cu_dan')
            ->leftJoin('can_ho as c', 'c.id_can_ho', '=', 'y.id_can_ho')
            ->leftJoinSub($latestAssignment, 'latest_p', function ($join) {
                $join->on('latest_p.id_yeu_cau', '=', 'y.id_yeu_cau');
            })
            ->leftJoin('phan_cong as p', 'p.id_phan_cong', '=', 'latest_p.latest_id_phan_cong')
            ->select(
                'y.*',
                'l.ten_loai',
                'p.id_phan_cong',
                'p.ngay_phan_cong',
                'p.gio_hen',
                'p.trang_thai as trang_thai_phan_cong',
                'nd.ten as ten_chu_ho',
                'nd.dien_thoai as sdt_chu_ho',
                'c.so_can_ho'
            );

        if ($request->filled('id_cu_dan')) {
            $query->where('y.id_cu_dan', $request->query('id_cu_dan'));
        }

        return $query
            ->orderByDesc('y.created_at')
            ->orderByDesc('y.id_yeu_cau')
            ->get()
            ->map(function ($row) {
                $item = (array) $row;
                $item['loai_su_co'] = ['ten_loai' => $row->ten_loai];
                $item['chu_ho'] = [
                    'ten' => $row->ten_chu_ho,
                    'dien_thoai' => $row->sdt_chu_ho
                ];
                $item['can_ho'] = [
                    'so_can_ho' => $row->so_can_ho
                ];
                $item['phan_cong'] = [
                    'id_phan_cong' => $row->id_phan_cong,
                    'ngay_phan_cong' => $row->ngay_phan_cong,
                    'gio_hen' => $row->gio_hen,
                    'trang_thai' => $row->trang_thai_phan_cong,
                ];
                $item['da_xac_nhan'] = in_array($row->trang_thai, ['da_xac_nhan', 'dang_xu_ly', 'hoan_thanh'], true)
                    || !empty($row->id_phan_cong);
                unset($item['ten_loai']);
                unset($item['ten_chu_ho'], $item['sdt_chu_ho'], $item['so_can_ho']);
                unset($item['id_phan_cong'], $item['ngay_phan_cong'], $item['gio_hen'], $item['trang_thai_phan_cong']);
                return $item;
            });
    }

    public function show($id)
    {
        return DB::table('yeu_cau_bao_tri')->where('id_yeu_cau', $id)->first();
    }

    public function store(Request $request)
    {
        $data = $request->only(['id_cu_dan','id_can_ho','id_loai_su_co','mo_ta','thoi_gian_uu_tien','trang_thai']);
        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = DB::table('yeu_cau_bao_tri')->insertGetId($data);
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

    public function changeStatus(Request $request, $id)
    {
        $status = $request->input('status');
        DB::table('yeu_cau_bao_tri')->where('id_yeu_cau', $id)->update(['trang_thai' => $status]);

        // Note: the frontend may also submit scheduleDate and staff, but those belong in phan_cong table.
        // For now, updating the status of the request.
        return response()->json(['message' => 'Cập nhật trạng thái thành công']);
    }
}
