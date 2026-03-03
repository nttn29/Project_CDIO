<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TechnicianSeeder extends Seeder
{
    /**
     * Seed technician (kỹ thuật viên) accounts, assignments,
     * and work-log entries (nhật ký công việc).
     */
    public function run(): void
    {
        // ─── 1. KỸ THUẬT VIÊN ────────────────────────────────────────────
        $technicians = [
            [
                'email'      => 'tech1@example.com',
                'ten'        => 'Nguyễn Minh Tuấn',
                'mat_khau'   => Hash::make('password123'),
                'dien_thoai' => '0901234567',
                'vai_tro'    => 'ky_thuat_vien',
                'trang_thai' => 'active',
            ],
            [
                'email'      => 'tech2@example.com',
                'ten'        => 'Trần Quang Huy',
                'mat_khau'   => Hash::make('password123'),
                'dien_thoai' => '0912345670',
                'vai_tro'    => 'ky_thuat_vien',
                'trang_thai' => 'active',
            ],
            [
                'email'      => 'tech3@example.com',
                'ten'        => 'Lê Thị Mai',
                'mat_khau'   => Hash::make('password123'),
                'dien_thoai' => '0923456701',
                'vai_tro'    => 'ky_thuat_vien',
                'trang_thai' => 'active',
            ],
            [
                'email'      => 'tech4@example.com',
                'ten'        => 'Phạm Văn Đức',
                'mat_khau'   => Hash::make('password123'),
                'dien_thoai' => '0934567012',
                'vai_tro'    => 'ky_thuat_vien',
                'trang_thai' => 'active',
            ],
            [
                'email'      => 'tech5@example.com',
                'ten'        => 'Hoàng Thị Lan',
                'mat_khau'   => Hash::make('password123'),
                'dien_thoai' => '0945670123',
                'vai_tro'    => 'ky_thuat_vien',
                'trang_thai' => 'active',
            ],
        ];

        foreach ($technicians as $tech) {
            DB::table('nguoi_dung')->insertOrIgnore($tech);
        }

        // Lấy lại ID của 5 kỹ thuật viên vừa tạo
        $techIds = DB::table('nguoi_dung')
            ->where('vai_tro', 'ky_thuat_vien')
            ->pluck('id_nguoi_dung')
            ->values()
            ->toArray();

        if (empty($techIds)) {
            $this->command->warn('Không tìm thấy kỹ thuật viên, bỏ qua phần phân công.');
            return;
        }

        // Lấy các yêu cầu bảo trì đã có (từ ClientSeeder)
        $requestIds = DB::table('yeu_cau_bao_tri')
            ->pluck('id_yeu_cau')
            ->values()
            ->toArray();

        // ─── 2. PHÂN CÔNG ─────────────────────────────────────────────────
        // Mỗi yêu cầu gán cho 1 kỹ thuật viên phù hợp
        $assignments = [];

        if (!empty($requestIds)) {
            $assignments = [
                [
                    'id_yeu_cau'         => $requestIds[0] ?? null,  // Nước rỉ trần
                    'id_ky_thuat_vien'   => $techIds[0],              // Nguyễn Minh Tuấn
                    'ngay_phan_cong'     => now()->subDays(5)->toDateString(),
                    'gio_hen'            => '08:00:00',
                    'trang_thai'         => 'hoan_thanh',
                    'created_at'         => now()->subDays(5),
                    'updated_at'         => now()->subDays(1),
                ],
                [
                    'id_yeu_cau'         => $requestIds[1] ?? null,  // Đèn phòng khách
                    'id_ky_thuat_vien'   => $techIds[1],              // Trần Quang Huy
                    'ngay_phan_cong'     => now()->subDays(3)->toDateString(),
                    'gio_hen'            => '14:00:00',
                    'trang_thai'         => 'hoan_thanh',
                    'created_at'         => now()->subDays(3),
                    'updated_at'         => now()->subDays(2),
                ],
                [
                    'id_yeu_cau'         => $requestIds[2] ?? null,  // Điều hoà không lạnh
                    'id_ky_thuat_vien'   => $techIds[2],              // Lê Thị Mai
                    'ngay_phan_cong'     => now()->subDay()->toDateString(),
                    'gio_hen'            => '09:30:00',
                    'trang_thai'         => 'dang_xu_ly',
                    'created_at'         => now()->subDay(),
                    'updated_at'         => now()->subHours(2),
                ],
                [
                    'id_yeu_cau'         => $requestIds[3] ?? null,  // Mất internet
                    'id_ky_thuat_vien'   => $techIds[3],              // Phạm Văn Đức
                    'ngay_phan_cong'     => now()->toDateString(),
                    'gio_hen'            => '10:00:00',
                    'trang_thai'         => 'mo',
                    'created_at'         => now()->subHours(4),
                    'updated_at'         => now()->subHours(4),
                ],
            ];
        }

        // Thêm thêm vài phân công giả sử cho các yêu cầu mới
        $extraRequests = [
            [
                'id_cu_dan'           => DB::table('nguoi_dung')->where('vai_tro', 'cu_dan')->value('id_nguoi_dung'),
                'id_can_ho'           => DB::table('can_ho')->value('id_can_ho'),
                'id_loai_su_co'       => 7, // Cửa/Khóa
                'mo_ta'               => 'Khóa cửa chính bị hỏng',
                'thoi_gian_uu_tien'   => 'gan',
                'trang_thai'          => 'da_phan_cong',
                'created_at'          => now()->subDays(2),
                'updated_at'          => now()->subDays(2),
            ],
            [
                'id_cu_dan'           => DB::table('nguoi_dung')->where('vai_tro', 'cu_dan')->value('id_nguoi_dung'),
                'id_can_ho'           => DB::table('can_ho')->value('id_can_ho'),
                'id_loai_su_co'       => 6, // Tường/Sàn
                'mo_ta'               => 'Nứt tường phòng khách',
                'thoi_gian_uu_tien'   => 'binh_thuong',
                'trang_thai'          => 'moi',
                'created_at'          => now()->subHours(12),
                'updated_at'          => now()->subHours(12),
            ],
        ];

        foreach ($extraRequests as $req) {
            if ($req['id_cu_dan'] && $req['id_can_ho']) {
                $newReqId = DB::table('yeu_cau_bao_tri')->insertGetId($req);

                // Phân công yêu cầu "Khóa cửa" cho kỹ thuật viên thứ 5
                if ($req['trang_thai'] === 'da_phan_cong') {
                    $assignments[] = [
                        'id_yeu_cau'       => $newReqId,
                        'id_ky_thuat_vien' => $techIds[4],  // Hoàng Thị Lan
                        'ngay_phan_cong'   => now()->subDay()->toDateString(),
                        'gio_hen'          => '15:00:00',
                        'trang_thai'       => 'dang_xu_ly',
                        'created_at'       => now()->subDays(2),
                        'updated_at'       => now()->subDay(),
                    ];
                }
            }
        }

        foreach ($assignments as $assignment) {
            if (!empty($assignment['id_yeu_cau'])) {
                DB::table('phan_cong')->insertOrIgnore($assignment);
            }
        }

        // ─── 3. NHẬT KÝ CÔNG VIỆC (Work logs) ─────────────────────────────
        $phanCongIds = DB::table('phan_cong')
            ->where('trang_thai', 'hoan_thanh')
            ->pluck('id_phan_cong')
            ->values()
            ->toArray();

        $workLogs = [];

        if (isset($phanCongIds[0])) {
            $workLogs[] = [
                'id_phan_cong'     => $phanCongIds[0],
                'mo_ta_cong_viec'  => 'Kiểm tra trần nhà tầng 2. Phát hiện ống thoát nước bị nứt dưới sàn tầng 3. Khoan mở, thay ống PVC mới, trám xi măng chống thấm.',
                'so_gio_lam'       => 2.5,
                'chi_phi'          => 450000,
                'ngay_nhat_ky'     => now()->subDays(4)->toDateString(),
                'created_at'       => now()->subDays(4),
                'updated_at'       => now()->subDays(4),
            ];
        }

        if (isset($phanCongIds[1])) {
            $workLogs[] = [
                'id_phan_cong'     => $phanCongIds[1],
                'mo_ta_cong_viec'  => 'Kiểm tra tủ điện phòng khách. Cầu dao MCB bị trip do quá tải. Thay cầu dao mới 16A, kiểm tra toàn bộ ổ cắm và đèn. Hệ thống hoạt động bình thường.',
                'so_gio_lam'       => 1.5,
                'chi_phi'          => 280000,
                'ngay_nhat_ky'     => now()->subDays(2)->toDateString(),
                'created_at'       => now()->subDays(2),
                'updated_at'       => now()->subDays(2),
            ];
        }

        foreach ($workLogs as $log) {
            DB::table('nhat_ky_cong_viec')->insert($log);
        }

        // ─── THÔNG BÁO KẾT QUẢ ────────────────────────────────────────────
        $this->command->info('✅ TechnicianSeeder hoàn thành!');
        $this->command->info('');
        $this->command->info('Tài khoản kỹ thuật viên (mật khẩu: password123):');
        $this->command->info('  📧 tech1@example.com  — Nguyễn Minh Tuấn  (Điện/Nước)');
        $this->command->info('  📧 tech2@example.com  — Trần Quang Huy    (Điện)');
        $this->command->info('  📧 tech3@example.com  — Lê Thị Mai        (Điều hoà/Cơ khí)');
        $this->command->info('  📧 tech4@example.com  — Phạm Văn Đức      (Mạng/Hạ tầng)');
        $this->command->info('  📧 tech5@example.com  — Hoàng Thị Lan     (Xây dựng/Cửa)');
        $this->command->info('');
        $this->command->info('Phân công: ' . count($assignments) . ' bản ghi đã thêm');
        $this->command->info('Nhật ký:   ' . count($workLogs) . ' bản ghi đã thêm');
    }
}
