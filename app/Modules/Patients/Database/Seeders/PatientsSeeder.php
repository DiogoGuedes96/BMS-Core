<?php

namespace App\Modules\Patients\Database\Seeders;

use App\Modules\Patients\Models\Patients;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class PatientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (env('BMS_CLIENT') == 'ASM') {
            $this->asmPatientModules();
        }
    }

    private function asmPatientModules() {
        $faker = Faker::create();
        $patientResponsibleIds = [];
        $transportFeatures = ['seat', 'wheelchair', "lifter"];
        foreach (range(1, 20) as $index) {
            $patientResponsibleId = DB::table('patient_responsible')->insertGetId([
                'patient_responsible' => $faker->name(),
                'phone_number' => $faker->numberBetween(91000000, 999999999),
            ]);

            $patientResponsibleIds[] = $patientResponsibleId;
        }

        foreach (range(1, 50) as $index) {
            $patientId = DB::table('patients')->insertGetId([
                'name' => $faker->name(),
                'patient_number' => $faker->numberBetween(1, 999999999),
                'phone_number' => $faker->numberBetween(91000000, 999999999),
                'nif' => $faker->numberBetween(10000000, 999999999),
                'birthday' => $faker->date(),
                'email' => $faker->numberBetween(0, 1) ? $faker->email() : "",
                'address' => $faker->address(),
                'postal_code' => $faker->numberBetween(1000000, 9999999),
                'postal_code_address' => $faker->city(),
                'transport_feature' => $faker->randomElement($transportFeatures),
                'patient_observations' => $faker->text(255),
                'status' => $faker->numberBetween(0, 1),
            ]);

            $numberOfResponsibles = $faker->numberBetween(1, 3);
            $responsiblesForPatient = $faker->randomElements($patientResponsibleIds, $numberOfResponsibles, false);

            foreach ($responsiblesForPatient as $responsibleId) {
                DB::table('patient_have_responsible')->insert([
                    'patient_id' => $patientId,
                    'patient_responsible_id' => $responsibleId,
                ]);
            }
        }
    }
}
