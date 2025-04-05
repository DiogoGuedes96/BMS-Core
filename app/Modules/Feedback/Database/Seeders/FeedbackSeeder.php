<?php

namespace App\Modules\Feedback\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (env('BMS_CLIENT') == 'ASM') {
            $this->asmFeedbackModules();
        }
    }

    private function asmFeedbackModules() {
        $faker = Faker::create();

        foreach (range(1, 20) as $index) {
            $schedule = $faker->unique()->numberBetween(1, 20);
            $feedbackId = DB::table('feedback')->insertGetId([
                'name' => $faker->name(),
                'patient_number' => $faker->numberBetween(100000000, 999999999),
                'reason' => $faker->randomElement(['Elogio', 'Reclamação']),
                'date' => $faker->date(),
                'time' => $faker->time('H:i'),
                'description' => $faker->paragraph(),
                'schedule' => ($schedule % 2 !== 0) ? $schedule : 0
            ]);

            $numberOfFeedbackWhos = $faker->numberBetween(1, 3);
            foreach (range(1, $numberOfFeedbackWhos) as $index) {
                DB::table('feedback_who')->insertGetId([
                    'name' => $faker->name(),
                    'feedback_id' => $feedbackId
                ]);
            }
        }
    }
}
