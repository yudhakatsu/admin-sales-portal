<?php

namespace Database\Seeders;

use App\Models\User;
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            // Seeder lainnya bisa ditambahkan di sini
        ]);
    }
}
