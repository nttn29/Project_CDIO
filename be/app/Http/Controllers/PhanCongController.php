<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * PhanCongController – Quản lý phân công kỹ thuật viên cho yêu cầu bảo trì.
 *
 * Khi admin tạo một phân công mới (store), controller này tự động đồng bộ
 * dữ liệu sang bảng technician_jobs để ứng dụng kỹ thuật viên có thể nhìn
 * thấy ngay công việc mới được giao mà không cần can thiệp thêm.
 */
class PhanCongController extends Controller
{
    /** Lấy toàn bộ danh sách phân công */
    public function index()
    {
        return DB::table('phan_cong')->get();
    }

    /** Lấy thông tin một phân công theo ID */
    public function show($id)
    {
        return DB::table('phan_cong')->where('id_phan_cong', $id)->first();
    }

    /**
     * Tạo một phân công mới và đồng bộ sang bảng technician_jobs.
     *
     * Luồng xử lý:
     *  1. Chèn bản ghi mới vào bảng phan_cong.
     *  2. Truy vấn thông tin yeu_cau_bao_tri liên quan (tiêu đề, căn hộ, ưu tiên).
     *  3. Nếu bảng technician_jobs tồn tại → upsert bản ghi tương ứng với
     *     code = "PC-{id_phan_cong}" để ứng dụng kỹ thuật viên nhận được việc.
     *
     * @param Request $request Dữ liệu phân công: id_yeu_cau, id_ky_thuat_vien,
     *                          ngay_phan_cong, gio_hen, trang_thai
     */
    public function store(Request $request)
    {
        $data = $request->only(['id_yeu_cau', 'id_ky_thuat_vien', 'ngay_phan_cong', 'gio_hen', 'trang_thai']);
        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = DB::table('phan_cong')->insertGetId($data);

        // ── Đồng bộ dữ liệu sang module Technician ──────────────────────
        // Lấy thông tin yêu cầu bảo trì kèm loại sự cố và số căn hộ
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

        // Chỉ đồng bộ nếu tìm được yêu cầu và bảng technician_jobs đã tồn tại
        if ($requestRow && Schema::hasTable('technician_jobs')) {
            // Gộp ngay_phan_cong + gio_hen thành datetime scheduled_at
            $scheduledAt = null;
            if (!empty($data['ngay_phan_cong'])) {
                $scheduledAt = !empty($data['gio_hen'])
                    ? "{$data['ngay_phan_cong']} {$data['gio_hen']}:00"
                    : "{$data['ngay_phan_cong']} 09:00:00";
            }

            $title       = $requestRow->ten_loai ?: 'Yeu cau bao tri';
            $location    = $requestRow->so_can_ho ? ("Can ho " . $requestRow->so_can_ho) : null;
            $description = $requestRow->mo_ta ?: null;
            $priority    = $this->mapPriority($requestRow->thoi_gian_uu_tien);

            // Upsert: tạo mới hoặc cập nhật nếu đã tồn tại bản ghi có cùng code
            DB::table('technician_jobs')->updateOrInsert(
                ['code' => 'PC-' . $id],
                [
                    'title'         => Str::limit($title, 255, ''),
                    'location'      => $location,
                    'description'   => $description,
                    'status'        => 'moi',      // trạng thái ban đầu: chưa xử lý
                    'priority'      => $priority,
                    'scheduled_at'  => $scheduledAt,
                    'due_at'        => null,
                    'technician_id' => $data['id_ky_thuat_vien'] ?? null,
                    'updated_at'    => now(),
                    'created_at'    => now(),
                ]
            );
        }

        return response()->json(['id' => $id], 201);
    }

    /** Cập nhật thông tin một phân công theo ID */
    public function update(Request $request, $id)
    {
        DB::table('phan_cong')->where('id_phan_cong', $id)->update($request->except(['id_phan_cong']));
        return response()->noContent();
    }

    /** Xoá một phân công theo ID */
    public function destroy($id)
    {
        DB::table('phan_cong')->where('id_phan_cong', $id)->delete();
        return response()->noContent();
    }

    /**
     * Chuyển đổi giá trị thoi_gian_uu_tien của yêu cầu bảo trì
     * sang mức ưu tiên chuẩn (thap / trung_binh / cao) cho bảng technician_jobs.
     *
     * Bảng ánh xạ:
     *  khan_cap / cao → cao
     *  thap           → thap
     *  (mặc định)     → trung_binh
     *
     * @param string|null $value Giá trị thoi_gian_uu_tien từ yeu_cau_bao_tri
     */
    private function mapPriority(?string $value): string
    {
        return match ($value) {
            'khan_cap', 'cao' => 'cao',
            'thap'            => 'thap',
            default           => 'trung_binh',
        };
    }
}

