<?php

namespace App\Modules\AmbulanceCrew\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class AmbulanceCrewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (env('BMS_CLIENT') == 'ASM') {
            $this->createAmbulanceCrew();
        }
    }
    public function createAmbulanceCrew(): void
    {
        $faker = Faker::create();
        foreach (range(1, 20) as $index) {
            DB::table('ambulance_crew')->insert([
                'name' => $faker->name(),
                'email' => $faker->email(),
                'nif' => $faker->numberBetween(10000000, 999999999),
                'phone_number' => $faker->numberBetween(91000000, 999999999),
                'driver_license' => 'L-'.$faker->numberBetween(1, 9999999),
                'contract_date' => $faker->date(),
                'contract_number' => $faker->numberBetween(1, 999999999),
                'job_title' => $faker->numberBetween(0, 1) ? 'driver' : 'tat',
                'address' => $faker->address(),
                'status' => $faker->numberBetween(0, 1) ? true : false,
                'created_at' => Carbon::now()
            ]);
        }
    }
}
