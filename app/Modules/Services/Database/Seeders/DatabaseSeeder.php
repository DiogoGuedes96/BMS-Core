<?php

namespace App\Modules\Services\Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ServiceStateSeeder::class
        ]);
    }
}
