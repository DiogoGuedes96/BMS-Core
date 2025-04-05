<?php

namespace App\Modules\ServiceScheduling\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ServiceSchedulingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (env('BMS_CLIENT') == 'ASM') {
            $this->asmServiceScheduling();
        }
    }
    public function asmServiceScheduling()
    {
        $faker = Faker::create();
        $transportFeatures = ['seat', 'wheelchair', "lifter"];
        $patientsStatus = ['credential', 'not_credential'];
        for ($i = 1; $i <= 20; $i++) {
            DB::table('bms_service_scheduling')->insert([
                'user_id' => rand(1, 10), // Assuming you have user IDs from 1 to 10
                'client_id' => rand(1, 10), // Assuming you have client IDs from 1 to 10
                'reason' => 'agendamento',
                'additional_note' => 'Sample additional note ' . $i,
                'patients_status' => $faker->randomElement($patientsStatus),
                'patient_id' => rand(1, 20), // Assuming you have patient IDs from 1 to 20
                'transport_feature' => $faker->randomElement($transportFeatures),
                'service_type' => 'Fisioterapia', // You can change this as needed
                'date' => now(),
                'time' => '14:00:00', // You can change the time as needed
                'origin' => 'Sample Origin ' . $i,
                'destination' => 'Sample Destination ' . $i,
                'vehicle' => 'VDTD', // You can change the vehicle as needed
                'license_plate' => 'ABC' . $i . '123',
                'responsible_tats_1' => 'TAT1',
                'responsible_tats_2' => 'TAT2',
                'companion' => ($i % 5 == 0), // Every 5th record has a companion
                'companion_name' => 'Companion ' . $i,
                'companion_contact' => 9876543210 + $i,
                'transport_justification' => 'Sample justification ' . $i,
                'payment_method' => 'transferência bancária', // You can change the payment method as needed
                'total_value' => 100.00 + ($i * 10), // Increase the total value for each record
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'is_back_service' => 'no',
            ]);
        }
    }
}
