<?php

namespace App\Modules\Calls\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void {
        $this->call([
            ASMCallsSeeder::class,
        ]);
    }
}
