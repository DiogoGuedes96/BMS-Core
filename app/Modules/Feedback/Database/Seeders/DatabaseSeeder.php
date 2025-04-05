<?php

namespace App\Modules\Feedback\Database\Seeders;

use App\Modules\Feedback\Database\Seeders\FeedbackSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (env('BMS_CLIENT') === 'ASM') {
            $this->call([
                FeedbackSeeder::class
            ]);
        }
    }
}
