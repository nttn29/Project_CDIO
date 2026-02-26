<?php

namespace App\Http\Controllers;

use App\Models\ChuCanHo;
use Illuminate\Http\Request;

class Chu_can_hoController extends Controller
{
    // GET /api/chu-can-ho
    public function index()
    {
        return response()->json(ChuCanHo::all());
    }

    // POST /api/chu-can-ho
    public function store(Request $request)
    {
        $request->validate([
            'ho_ten' => 'required|string|max:255',
            'cccd' => 'required|string|max:20|unique:chu_can_ho,cccd',
            'so_dien_thoai' => 'required|string|max:15',
            'dia_chi_thuong_tru' => 'required|string',
            'so_nha' => 'required|string|max:20',
            'ngay_dang_ky' => 'required|date'
        ]);

        $chuCanHo = ChuCanHo::create($request->all());

        return response()->json($chuCanHo, 201);
    }

    // GET /api/chu-can-ho/{id}
    public function show(string $id)
    {
        $chuCanHo = ChuCanHo::findOrFail($id);
        return response()->json($chuCanHo);
    }

    // PUT /api/chu-can-ho/{id}
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

    // Nếu phân công thì chuyển trạng thái kỹ thuật viên thành busy
    if ($request->technician_id) {
        \App\Models\KyThuatVien::where('id', $request->technician_id)
            ->update(['trang_thai' => 'busy']);
    }

    return response()->json(['message' => 'Đã cập nhật']);
}

    // DELETE /api/chu-can-ho/{id}
    public function destroy(string $id)
    {
        $chuCanHo = ChuCanHo::findOrFail($id);
        $chuCanHo->delete();

        return response()->json([
            'message' => 'Xoá thành công'
        ]);
    }
    public function soDo()
{
    $rooms = [];

    // Lấy tất cả số nhà đã có người
    $chuCanHo = \App\Models\ChuCanHo::all();

    foreach ($chuCanHo as $item) {
        $rooms[$item->so_nha] = [
            'occupied' => true,
            'requestCount' => 0,
            'scheduled' => false
        ];
    }
    return response()->json($rooms);
}
}