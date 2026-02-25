<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create issue types (Loại sự cố)
        $issueTypes = [
            ['ten_loai' => 'Điện', 'muc_uu_tien' => 3, 'mo_ta' => 'Vấn đề liên quan đến điện'],
            ['ten_loai' => 'Nước', 'muc_uu_tien' => 3, 'mo_ta' => 'Vấn đề liên quan đến nước'],
            ['ten_loai' => 'Thang máy', 'muc_uu_tien' => 4, 'mo_ta' => 'Thang máy không hoạt động'],
            ['ten_loai' => 'Điều hoà', 'muc_uu_tien' => 2, 'mo_ta' => 'Điều hoà không lạnh'],
            ['ten_loai' => 'Internet', 'muc_uu_tien' => 1, 'mo_ta' => 'Mất kết nối internet'],
            ['ten_loai' => 'Tường/Sàn', 'muc_uu_tien' => 2, 'mo_ta' => 'Nứt tường hoặc sàn'],
            ['ten_loai' => 'Cửa/Khóa', 'muc_uu_tien' => 3, 'mo_ta' => 'Cửa hỏng hoặc khóa bị trục trặc'],
            ['ten_loai' => 'Khác', 'muc_uu_tien' => 1, 'mo_ta' => 'Vấn đề khác'],
        ];

        foreach ($issueTypes as $type) {
            DB::table('loai_su_co')->insertOrIgnore($type);
        }

        // Create building (Tòa nhà)
        $buildingId = DB::table('toa_nha')->insertGetId([
            'ten_toa_nha' => 'Tòa nhà A',
            'dia_chi' => '123 Đường Lê Lợi, Quận 1, TP.HCM',
        ]);

        // Create apartments (Căn hộ)
        for ($floor = 1; $floor <= 3; $floor++) {
            for ($unit = 1; $unit <= 4; $unit++) {
                DB::table('can_ho')->insert([
                    'id_toa_nha' => $buildingId,
                    'so_can_ho' => $floor . '0' . $unit,
                    'tang' => $floor,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Create sample residents (Cư dân)
        $residents = [
            [
                'email' => 'resident1@example.com',
                'ten' => 'Nguyễn Văn A',
                'mat_khau' => Hash::make('password123'),
                'dien_thoai' => '0912345678',
                'vai_tro' => 'cu_dan',
                'trang_thai' => 'active',
            ],
            [
                'email' => 'resident2@example.com',
                'ten' => 'Trần Thị B',
                'mat_khau' => Hash::make('password123'),
                'dien_thoai' => '0987654321',
                'vai_tro' => 'cu_dan',
                'trang_thai' => 'active',
            ],
            [
                'email' => 'resident3@example.com',
                'ten' => 'Lê Văn C',
                'mat_khau' => Hash::make('password123'),
                'dien_thoai' => '0913456789',
                'vai_tro' => 'cu_dan',
                'trang_thai' => 'active',
            ],
            [
                'email' => 'staff@example.com',
                'ten' => 'Quản lý Sửa chữa',
                'mat_khau' => Hash::make('password123'),
                'dien_thoai' => '0934567890',
                'vai_tro' => 'nhan_vien',
                'trang_thai' => 'active',
            ],
        ];

        foreach ($residents as $resident) {
            DB::table('nguoi_dung')->insertOrIgnore($resident);
        }

        // Assign residents to apartments
        $residentIds = DB::table('nguoi_dung')->where('vai_tro', 'cu_dan')->pluck('id_nguoi_dung')->toArray();
        $apartmentIds = DB::table('can_ho')->pluck('id_can_ho')->toArray();

        foreach (array_slice($residentIds, 0, 3) as $index => $residentId) {
            if (isset($apartmentIds[$index])) {
                DB::table('can_ho')
                    ->where('id_can_ho', $apartmentIds[$index])
                    ->update(['id_cu_dan' => $residentId]);
            }
        }

        // Create sample maintenance requests
        $requests = [
            [
                'id_cu_dan' => $residentIds[0],
                'id_can_ho' => $apartmentIds[0],
                'id_loai_su_co' => 2, // Water issue
                'mo_ta' => 'Nước rỉ từ trần phòng ngủ',
                'thoi_gian_uu_tien' => 'gan',
                'trang_thai' => 'hoan_thanh',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(1),
            ],
            [
                'id_cu_dan' => $residentIds[1],
                'id_can_ho' => $apartmentIds[1],
                'id_loai_su_co' => 1, // Electricity issue
                'mo_ta' => 'Đèn phòng khách không sáng',
                'thoi_gian_uu_tien' => 'binh_thuong',
                'trang_thai' => 'hoan_thanh',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(2),
            ],
            [
                'id_cu_dan' => $residentIds[2],
                'id_can_ho' => $apartmentIds[2],
                'id_loai_su_co' => 4, // AC issue
                'mo_ta' => 'Điều hoà không lạnh',
                'thoi_gian_uu_tien' => 'kho',
                'trang_thai' => 'dang_xu_ly',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subHours(2),
            ],
            [
                'id_cu_dan' => $residentIds[0],
                'id_can_ho' => $apartmentIds[0],
                'id_loai_su_co' => 5, // Internet issue
                'mo_ta' => 'Mất kết nối internet',
                'thoi_gian_uu_tien' => 'gan',
                'trang_thai' => 'moi',
                'created_at' => now()->subHours(6),
                'updated_at' => now()->subHours(6),
            ],
        ];

        foreach ($requests as $request) {
            DB::table('yeu_cau_bao_tri')->insert($request);
        }

        $feedback = [
            [
                'id_yeu_cau' => 1,
                'id_cu_dan' => $residentIds[0],
                'danh_gia' => 5,
                'binh_luan' => 'Dịch vụ tuyệt vời! Nhân viên rất chuyên nghiệp và nhanh chóng.',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'id_yeu_cau' => 2,
                'id_cu_dan' => $residentIds[1],
                'danh_gia' => 4,
                'binh_luan' => 'Tốt lắm nhưng phải chờ hơi lâu',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
        ];

        foreach ($feedback as $f) {
            DB::table('phan_hoi')->insert($f);
        }

        echo "✅ Resident seeder completed!\n";
        echo "Sample residents:\n";
        echo "  - resident1@example.com (password: password123)\n";
        echo "  - resident2@example.com (password: password123)\n";
        echo "  - resident3@example.com (password: password123)\n";
        echo "  - staff@example.com (password: password123)\n";
    }
}
