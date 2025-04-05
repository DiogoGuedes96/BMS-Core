<?php

namespace App\Modules\Users\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Modules\Users\Models\UserProfile;

class UserProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (env('BMS_CLIENT') == 'FRUTARIA') {
            $this->frutariaProfileUsers();
        }

        if (env('BMS_CLIENT') == 'ASM') {
            $this->asmProfileUsers();
        }

        if (env('BMS_CLIENT') == 'ATRAVEL') {
            $this->atravelProfileUsers();
        }

        if (env('BMS_CLIENT') == 'UNI') {
            $this->uniProfileUsers();
        }
    }

    public function uniProfileUsers()
    {
        $profiles = [
            'admin' => 'Administrador',
            'referrer' => 'Referenciador',
            'business_coach' => 'Business Coach',
            'closer' => 'Closer',
        ];

        foreach($profiles as $role => $description) {
            $readonly = true;

            if (!UserProfile::where('role', '=', $role)->exists()) {
                UserProfile::create(compact('role', 'description', 'readonly'));
            } else {
                DB::table('user_profile')->where('role', '=', $role)
                    ->update(compact('role', 'description', 'readonly'));
            }
        }
    }

    public function frutariaProfileUsers()
    {
        if (!DB::table('user_profile')->where('role', 'admin')->exists()) {
            DB::table('user_profile')->insert([
                'role'  => 'admin',
                'description' => 'Administrador',
            ]);
        }
        if (!DB::table('user_profile')->where('role', 'shipper')->exists()) {
            DB::table('user_profile')->insert([
                'role'  => 'shipper',
                'description' => 'System shipper',
            ]);
        }
        if (!DB::table('user_profile')->where('role', 'attendant')->exists()) {
            DB::table('user_profile')->insert([
                'role'  => 'attendant',
                'description' => 'System attendant',
            ]);
        }
    }

    public function asmProfileUsers()
    {
        if (!DB::table('user_profile')->where('role', 'admin')->exists()) {
            DB::table('user_profile')->insert([
                'role'  => 'admin',
                'description' => 'Administrador',
            ]);
        }

        if (!DB::table('user_profile')->where('role', 'coordinator')->exists()) {
            DB::table('user_profile')->insert([
                'role'  => 'coordinator',
                'description' => 'Coordenador',
            ]);
        }

        if (!DB::table('user_profile')->where('role', 'accountancy')->exists()) {
            DB::table('user_profile')->insert([
                'role'  => 'accountancy',
                'description' => 'Contabilidade',
            ]);
        }

        if (!DB::table('user_profile')->where('role', 'tat')->exists()) {
            DB::table('user_profile')->insert([
                'role'  => 'tat',
                'description' => 'TAT',
            ]);
        }
    }

    public function atravelProfileUsers()
    {
        $profiles = [
            'administrator' => 'Administrador',
            'atravel' => 'Atravel',
            'operator' => 'Operador',
            'staff' => 'Staff',
        ];

        foreach($profiles as $role => $description) {
            $readonly = true;

            if (!UserProfile::where('role', '=', $role)->exists()) {
                UserProfile::create(compact('role', 'description', 'readonly'));
            } else {
                DB::table('user_profile')->where('role', '=', $role)
                    ->update(compact('role', 'description', 'readonly'));
            }
        }
    }
}
