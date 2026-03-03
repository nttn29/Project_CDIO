<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        // Run client/resident seeder first (tạo cư dân + yêu cầu bảo trì)
        $this->call(ClientSeeder::class);

        // Run technician seeder (tạo kỹ thuật viên + phân công + nhật ký)
        $this->call(TechnicianSeeder::class);

        // Run apartment owner seeder (tạo chủ căn hộ đầy đủ 5 tầng)
        $this->call(ApartmentOwnerSeeder::class);
    }
}
