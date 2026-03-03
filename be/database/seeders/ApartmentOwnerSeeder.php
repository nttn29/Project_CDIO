<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ApartmentOwnerSeeder extends Seeder
{
    /**
     * Seed apartment owners (chủ căn hộ / cư dân) and link them to apartments.
     * Also seeds additional maintenance requests for these residents.
     */
    public function run(): void
    {
        // ─── 1. TÒA NHÀ (lấy hoặc tạo mới) ─────────────────────────────────
        $buildingId = DB::table('toa_nha')->value('id_toa_nha');

        if (!$buildingId) {
            $buildingId = DB::table('toa_nha')->insertGetId([
                'ten_toa_nha' => 'Tòa nhà EcoHome',
                'dia_chi'     => '123 Đường Lê Lợi, Quận 1, TP.HCM',
            ]);
        }

        // ─── 2. CĂN HỘ: đảm bảo đủ 5 tầng × 4 căn = 20 căn ────────────────
        $existingApts = DB::table('can_ho')
            ->where('id_toa_nha', $buildingId)
            ->pluck('so_can_ho')
            ->toArray();

        for ($floor = 1; $floor <= 5; $floor++) {
            for ($unit = 1; $unit <= 4; $unit++) {
                $aptNum = $floor . '0' . $unit;   // 101, 102, 201, 202 …
                if (!in_array($aptNum, $existingApts)) {
                    DB::table('can_ho')->insert([
                        'id_toa_nha' => $buildingId,
                        'so_can_ho'  => $aptNum,
                        'tang'       => $floor,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Lấy tất cả id căn hộ, sắp xếp theo số
        $aptIds = DB::table('can_ho')
            ->where('id_toa_nha', $buildingId)
            ->orderBy('tang')
            ->orderBy('so_can_ho')
            ->pluck('id_can_ho')
            ->values()
            ->toArray();

        // ─── 3. CHỦ CĂN HỘ (cư dân) ─────────────────────────────────────────
        $owners = [
            // Tầng 1
            ['ten' => 'Nguyễn Văn An',         'email' => 'owner101@example.com', 'phone' => '0901111001'],
            ['ten' => 'Trần Thị Bích',          'email' => 'owner102@example.com', 'phone' => '0901111002'],
            ['ten' => 'Lê Minh Cường',          'email' => 'owner103@example.com', 'phone' => '0901111003'],
            ['ten' => 'Phạm Thị Dung',          'email' => 'owner104@example.com', 'phone' => '0901111004'],
            // Tầng 2
            ['ten' => 'Hoàng Văn Em',           'email' => 'owner201@example.com', 'phone' => '0901111005'],
            ['ten' => 'Vũ Thị Phương',          'email' => 'owner202@example.com', 'phone' => '0901111006'],
            ['ten' => 'Đặng Quốc Việt',         'email' => 'owner203@example.com', 'phone' => '0901111007'],
            ['ten' => 'Bùi Thị Hương',          'email' => 'owner204@example.com', 'phone' => '0901111008'],
            // Tầng 3
            ['ten' => 'Ngô Văn Hải',            'email' => 'owner301@example.com', 'phone' => '0901111009'],
            ['ten' => 'Dương Thị Kim',          'email' => 'owner302@example.com', 'phone' => '0901111010'],
            ['ten' => 'Lý Minh Long',           'email' => 'owner303@example.com', 'phone' => '0901111011'],
            ['ten' => 'Mai Thị Ngọc',           'email' => 'owner304@example.com', 'phone' => '0901111012'],
            // Tầng 4
            ['ten' => 'Trương Văn Minh',        'email' => 'owner401@example.com', 'phone' => '0901111013'],
            ['ten' => 'Đinh Thị Oanh',          'email' => 'owner402@example.com', 'phone' => '0901111014'],
            ['ten' => 'Phan Quang Phú',         'email' => 'owner403@example.com', 'phone' => '0901111015'],
            ['ten' => 'Tô Thị Quỳnh',          'email' => 'owner404@example.com', 'phone' => '0901111016'],
            // Tầng 5
            ['ten' => 'Châu Văn Rì',           'email' => 'owner501@example.com', 'phone' => '0901111017'],
            ['ten' => 'Hồ Thị Sương',          'email' => 'owner502@example.com', 'phone' => '0901111018'],
            ['ten' => 'Lâm Minh Tài',          'email' => 'owner503@example.com', 'phone' => '0901111019'],
            ['ten' => 'Quách Thị Uyên',        'email' => 'owner504@example.com', 'phone' => '0901111020'],
        ];

        $ownerIds = [];
        foreach ($owners as $owner) {
            // Bỏ qua nếu email đã tồn tại
            DB::table('nguoi_dung')->insertOrIgnore([
                'email'      => $owner['email'],
                'ten'        => $owner['ten'],
                'mat_khau'   => Hash::make('password123'),
                'dien_thoai' => $owner['phone'],
                'vai_tro'    => 'cu_dan',
                'trang_thai' => 'active',
            ]);

            $id = DB::table('nguoi_dung')
                ->where('email', $owner['email'])
                ->value('id_nguoi_dung');

            if ($id) {
                $ownerIds[] = $id;
            }
        }

        // ─── 4. GÁN CHỦ CĂN HỘ VÀO TỪNG CĂN ─────────────────────────────────
        foreach ($ownerIds as $idx => $ownerId) {
            if (isset($aptIds[$idx])) {
                DB::table('can_ho')
                    ->where('id_can_ho', $aptIds[$idx])
                    ->whereNull('id_cu_dan')          // không ghi đè nếu đã có chủ
                    ->update([
                        'id_cu_dan'  => $ownerId,
                        'updated_at' => now(),
                    ]);
            }
        }

        // ─── 5. YÊU CẦU BẢO TRÌ MẪU (từ các chủ căn hộ mới) ───────────────
        // Lấy loại sự cố
        $loaiIds = DB::table('loai_su_co')->pluck('id_loai_su_co', 'ten_loai');

        $sampleRequests = [
            [
                'owner_email' => 'owner201@example.com',
                'apt_floor'   => 2, 'apt_unit' => '201',
                'loai'        => 'Điện', 'mo_ta' => 'Ổ cắm phòng ngủ bị chập, có tia lửa điện',
                'priority'    => 'gap', 'status' => 'moi', 'days_ago' => 0,
            ],
            [
                'owner_email' => 'owner302@example.com',
                'apt_floor'   => 3, 'apt_unit' => '302',
                'loai'        => 'Nước', 'mo_ta' => 'Đường ống dưới bồn rửa bếp bị rỉ nước',
                'priority'    => 'binh_thuong', 'status' => 'moi', 'days_ago' => 1,
            ],
            [
                'owner_email' => 'owner104@example.com',
                'apt_floor'   => 1, 'apt_unit' => '104',
                'loai'        => 'Điều hoà', 'mo_ta' => 'Điều hoà phòng khách chảy nước ra sàn',
                'priority'    => 'binh_thuong', 'status' => 'da_phan_cong', 'days_ago' => 3,
            ],
            [
                'owner_email' => 'owner403@example.com',
                'apt_floor'   => 4, 'apt_unit' => '403',
                'loai'        => 'Cửa/Khóa', 'mo_ta' => 'Cửa chính không khóa được, chốt bị kẹt',
                'priority'    => 'gap', 'status' => 'dang_xu_ly', 'days_ago' => 2,
            ],
            [
                'owner_email' => 'owner501@example.com',
                'apt_floor'   => 5, 'apt_unit' => '501',
                'loai'        => 'Tường/Sàn', 'mo_ta' => 'Vết nứt dài xuất hiện trên tường phòng khách sau mưa lớn',
                'priority'    => 'binh_thuong', 'status' => 'hoan_thanh', 'days_ago' => 7,
            ],
            [
                'owner_email' => 'owner203@example.com',
                'apt_floor'   => 2, 'apt_unit' => '203',
                'loai'        => 'Internet', 'mo_ta' => 'Đường truyền mạng yếu, thường xuyên mất kết nối',
                'priority'    => 'binh_thuong', 'status' => 'moi', 'days_ago' => 0,
            ],
            [
                'owner_email' => 'owner305@example.com',
                'apt_floor'   => 3, 'apt_unit' => '301',
                'loai'        => 'Điện', 'mo_ta' => 'Đèn hành lang trước cửa căn hộ bị hỏng 2 ngày nay',
                'priority'    => 'binh_thuong', 'status' => 'hoan_thanh', 'days_ago' => 5,
            ],
            [
                'owner_email' => 'owner101@example.com',
                'apt_floor'   => 1, 'apt_unit' => '101',
                'loai'        => 'Khác', 'mo_ta' => 'Mùi hôi bốc lên từ cống thoát sàn nhà vệ sinh',
                'priority'    => 'gap', 'status' => 'dang_xu_ly', 'days_ago' => 1,
            ],
        ];

        $insertedRequests = 0;
        foreach ($sampleRequests as $r) {
            $ownerId = DB::table('nguoi_dung')
                ->where('email', $r['owner_email'])
                ->value('id_nguoi_dung');

            $aptId = DB::table('can_ho')
                ->where('so_can_ho', $r['apt_unit'])
                ->value('id_can_ho');

            $loaiId = $loaiIds[$r['loai']] ?? null;

            if ($ownerId && $aptId) {
                DB::table('yeu_cau_bao_tri')->insert([
                    'id_cu_dan'         => $ownerId,
                    'id_can_ho'         => $aptId,
                    'id_loai_su_co'     => $loaiId,
                    'mo_ta'             => $r['mo_ta'],
                    'thoi_gian_uu_tien' => $r['priority'],
                    'trang_thai'        => $r['status'],
                    'created_at'        => now()->subDays($r['days_ago']),
                    'updated_at'        => now()->subDays(max(0, $r['days_ago'] - 1)),
                ]);
                $insertedRequests++;
            }
        }

        // ─── THÔNG BÁO ──────────────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('✅ ApartmentOwnerSeeder hoàn thành!');
        $this->command->info('');
        $this->command->info('📦 Căn hộ: 5 tầng × 4 căn = 20 căn hộ');
        $this->command->info('👤 Chủ căn hộ: ' . count($ownerIds) . ' tài khoản (mật khẩu: password123)');
        $this->command->info('📋 Yêu cầu bảo trì mẫu: ' . $insertedRequests . ' bản ghi');
        $this->command->info('');
        $this->command->info('Một số tài khoản mẫu:');
        $this->command->info('  📧 owner101@example.com  — Căn 101, Tầng 1');
        $this->command->info('  📧 owner201@example.com  — Căn 201, Tầng 2');
        $this->command->info('  📧 owner301@example.com  — Căn 301, Tầng 3');
        $this->command->info('  📧 owner401@example.com  — Căn 401, Tầng 4');
        $this->command->info('  📧 owner501@example.com  — Căn 501, Tầng 5');
    }
}
