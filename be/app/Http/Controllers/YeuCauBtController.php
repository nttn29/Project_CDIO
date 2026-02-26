<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YeuCauBt;

use App\Models\KyThuatVien;
class YeuCauBtController extends Controller
{
    // ==============================
    // 1. LẤY DANH SÁCH YÊU CẦU
    // ==============================
   public function index()
{
    $data = YeuCauBt::with(['chuCanHo', 'kyThuatVien'])
        ->orderByDesc('created_at')
        ->get();

    return response()->json(
        $data->map(function ($item) {
            return [
                'id' => $item->id,
                'owner' => $item->chuCanHo->ho_ten ?? '',
                'phone' => $item->chuCanHo->so_dien_thoai ?? '',
                'roomCode' => $item->chuCanHo->so_nha ?? '',
                'content' => $item->noi_dung,
                'status' => $item->trang_thai,

                // 🔥 THÊM DÒNG NÀY
                'technicianName' => $item->kyThuatVien->ten ?? '',
            ];
        })
    );
}
public function invoices()
{
    $data = YeuCauBt::with(['chuCanHo', 'kyThuatVien'])
        ->where('trang_thai', 'approved')
        ->orderByDesc('created_at')
        ->get();

    return response()->json(
        $data->map(function ($item) {
            return [
                'id' => $item->id,
                'owner' => $item->chuCanHo->ho_ten ?? '',
                'phone' => $item->chuCanHo->so_dien_thoai ?? '',
                'roomCode' => $item->chuCanHo->so_nha ?? '',
                'content' => $item->noi_dung,
                'technicianName' => $item->kyThuatVien->ten ?? '',
                'createdAt' => $item->created_at->format('d/m/Y'),
            ];
        })
    );
}

    // ==============================
    // 2. TẠO YÊU CẦU MỚI
    // ==============================
    public function store(Request $request)
    {
        $request->validate([
            'id_chu_can_ho' => 'required|exists:chu_can_ho,id',
            'noi_dung' => 'required|string',
        ]);

        $yeuCau = YeuCauBt::create([
            'id_chu_can_ho' => $request->id_chu_can_ho,
            'noi_dung' => $request->noi_dung,
        ]);

        return response()->json($yeuCau, 201);
    }

    // ==============================
    // 3. CẬP NHẬT TRẠNG THÁI
    // ==============================
   
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'technician_id' => 'nullable|exists:ky_thuat_vien,id',
        ]);

        $yeuCau = YeuCauBt::findOrFail($id);

        $yeuCau->update([
            'trang_thai' => $request->status,
            'id_ky_thuat_vien' => $request->technician_id,
        ]);

        // 🔥 ĐOẠN NÀY PHẢI Ở TRONG FUNCTION NÀY
        if ($request->technician_id) {
            KyThuatVien::where('id', $request->technician_id)
                ->update(['trang_thai' => 'busy']);
        }

        return response()->json(['message' => 'Đã cập nhật']);
    }
    // ==============================
    // 4. XÓA YÊU CẦU
    // ==============================
    public function destroy($id)
    {
        $yeuCau = YeuCauBt::findOrFail($id);
        $yeuCau->delete();

        return response()->json([
            'message' => 'Xóa thành công'
        ]);
    }
    public function show($id)
{
    $task = YeuCauBt::with(['chuCanHo', 'kyThuatVien'])
        ->findOrFail($id);

    return response()->json([
        'id' => $task->id,
        'createdAt' => $task->created_at->format('d/m/Y'),
        'customerName' => $task->chuCanHo->ho_ten ?? '',
        'phone' => $task->chuCanHo->so_dien_thoai ?? '',
        'houseNumber' => $task->chuCanHo->so_nha ?? '',
        'maintenanceContent' => $task->noi_dung,
        'technicianName' => $task->kyThuatVien->ten ?? '',
        'cost' => $task->chi_phi ?? 0
    ]);
}
}