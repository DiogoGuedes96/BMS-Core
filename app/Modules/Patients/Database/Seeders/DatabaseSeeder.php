<?php

namespace App\Modules\Patients\Database\Seeders;

use App\Modules\Patients\Database\Seeders\PatientsSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PatientsSeeder::class
        ]);
    }
}
