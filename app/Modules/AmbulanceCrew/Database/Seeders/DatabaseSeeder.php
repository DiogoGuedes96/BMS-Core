<?php

namespace App\Modules\AmbulanceCrew\Database\Seeders;

use  App\Modules\AmbulanceCrew\Database\Seeders\AmbulanceCrewSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AmbulanceCrewSeeder::class
        ]);
    }
}
