<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
        $data = $request->only(['id_yeu_cau', 'id_ky_thuat_vien', 'ngay_phan_cong', 'gio_hen', 'trang_thai']);
        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = DB::table('phan_cong')->insertGetId($data);

        // Keep Technician module in sync with admin assignment.
        $requestRow = DB::table('yeu_cau_bao_tri as y')
            ->leftJoin('loai_su_co as l', 'l.id_loai_su_co', '=', 'y.id_loai_su_co')
            ->leftJoin('can_ho as c', 'c.id_can_ho', '=', 'y.id_can_ho')
            ->where('y.id_yeu_cau', $data['id_yeu_cau'] ?? null)
            ->select(
                'y.id_yeu_cau',
                'y.mo_ta',
                'y.thoi_gian_uu_tien',
                'l.ten_loai',
                'c.so_can_ho'
            )
            ->first();

        if ($requestRow && Schema::hasTable('technician_jobs')) {
            $scheduledAt = null;
            if (!empty($data['ngay_phan_cong'])) {
                $scheduledAt = !empty($data['gio_hen'])
                    ? "{$data['ngay_phan_cong']} {$data['gio_hen']}:00"
                    : "{$data['ngay_phan_cong']} 09:00:00";
            }

            $title = $requestRow->ten_loai ?: 'Yeu cau bao tri';
            $location = $requestRow->so_can_ho ? ("Can ho " . $requestRow->so_can_ho) : null;
            $description = $requestRow->mo_ta ?: null;
            $priority = $this->mapPriority($requestRow->thoi_gian_uu_tien);

            DB::table('technician_jobs')->updateOrInsert(
                ['code' => 'PC-' . $id],
                [
                    'title' => Str::limit($title, 255, ''),
                    'location' => $location,
                    'description' => $description,
                    'status' => 'moi',
                    'priority' => $priority,
                    'scheduled_at' => $scheduledAt,
                    'due_at' => null,
                    'technician_id' => $data['id_ky_thuat_vien'] ?? null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

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

    private function mapPriority(?string $value): string
    {
        return match ($value) {
            'khan_cap', 'cao' => 'cao',
            'thap' => 'thap',
            default => 'trung_binh',
        };
    }
}
