<?php

namespace App\Modules\Clients\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void {
        $this->call([
            ASMClientsSeeder::class,
        ]);
    }
}
