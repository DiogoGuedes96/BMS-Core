<?php

namespace App\Modules\Services\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Modules\Services\Models\ServiceState;

class ServiceStateSeeder extends Seeder
{
    public function run(): void
    {
        $this->atravelStateServices();
    }

    public function atravelStateServices()
    {
        $serviceStates = [
            [
                'name' => 'Fechado',
                'is_default' => false
            ],
            [
                'name' => 'Aceite',
                'is_default' => false
            ],
            [
                'name' => 'Pendente',
                'is_default' => true
            ]
        ];

        foreach($serviceStates as $state) {
            $state['active'] = true;
            $state['readonly'] = true;

            if (!ServiceState::where('name', '=', $state['name'])->exists()) {
                ServiceState::create($state);
            } else {
                DB::table('bms_service_states')
                    ->where('name', '=', $state['name'])
                    ->update($state);
            }
        }
    }
}
