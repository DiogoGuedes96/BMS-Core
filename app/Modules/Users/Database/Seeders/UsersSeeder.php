<?php

namespace App\Modules\Users\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (env('BMS_CLIENT') == 'FRUTARIA') {
            $this->frutariaUsers();
        }

        if (env('BMS_CLIENT') == 'ASM') {
            $this->asmUsers();
        }

        if (env('BMS_CLIENT') == 'ATRAVEL') {
            $this->atravelUsers();
        }

        if (env('BMS_CLIENT') == 'UNI') {
            $this->uniUsers();
        }
    }

    public function uniUsers()
    {
        $commonsData = [
            'phone' => '987654321',
            'created_at' => now(),
            'updated_at' => now(),
            'password'   => '$2y$10$.fGpqE3MRQl6SAqgieiaBOWg3otctdAGm2aOtdYb8asCtOq3m0j22',
        ];

        $users = [
            [
                'name'       => 'User Administrador',
                'email'      => 'bmsadmin@integer.pt',
                'profile'   => 'admin'
            ],
            [
                'name'       => 'User Referenciador',
                'email'      => 'referrer@integer.pt',
                'profile'   => 'referrer'
            ],
            [
                'name'       => 'User Business Coach',
                'email'      => 'businesscoach@integer.pt',
                'profile'   => 'business_coach'
            ],
            [
                'name'       => 'User Closer',
                'email'      => 'closer@integer.pt',
                'profile'   => 'closer'
            ],
        ];

        foreach ($users as $user) {
            $userProfile = DB::table('user_profile')->where('role', '=', $user['profile'])->first();

            unset($user['profile']);

            if (!DB::table('users')->where('name', $user['name'])->exists()) {
                DB::table('users')->insert(
                    array_merge($user, $commonsData, ['profile_id' => $userProfile->id])
                );
            }
            // else {
            //     DB::table('users')->where('name', $user['name'])->update([
            //         'password' => $commonsData['password'],
            //         'updated_at' => $commonsData['updated_at']
            //     ]);
            // }
        }
    }

    public function asmUsers()
    {
        $users = [
            [
                'name'       => 'Administrador',
                'email'      => 'bmsadmin@integer.pt',
                'password'   => '$2y$10$.fGpqE3MRQl6SAqgieiaBOWg3otctdAGm2aOtdYb8asCtOq3m0j22',
                'phone'      => '987654321',
                'created_at' => now(),
                'updated_at' => now(),
                'profile'   => 'admin'
            ],
            [
                'name'       => 'Coordenador',
                'email'      => 'coordenador@integer.pt',
                'password'   => '$2y$10$.fGpqE3MRQl6SAqgieiaBOWg3otctdAGm2aOtdYb8asCtOq3m0j22',
                'phone'      => '987654321',
                'created_at' => now(),
                'updated_at' => now(),
                'profile'   => 'coordinator'
            ],
            [
                'name'       => 'Contabilidade',
                'email'      => 'contabilidade@integer.pt',
                'password'   => '$2y$10$.fGpqE3MRQl6SAqgieiaBOWg3otctdAGm2aOtdYb8asCtOq3m0j22',
                'phone'      => '987654321',
                'created_at' => now(),
                'updated_at' => now(),
                'profile'   => 'accountancy'
            ],
            [
                'name'       => 'TAT',
                'email'      => 'tat@integer.pt',
                'password'   => '$2y$10$.fGpqE3MRQl6SAqgieiaBOWg3otctdAGm2aOtdYb8asCtOq3m0j22',
                'phone'      => '987654321',
                'created_at' => now(),
                'updated_at' => now(),
                'profile'   => 'tat'
            ]
        ];

        foreach ($users as $user) {
            $userProfile = DB::table('user_profile')->where('role', '=', $user['profile'])->first();

            unset($user['profile']);

            if (!DB::table('users')->where('name', $user['name'])->exists()) {
                DB::table('users')->insert(array_merge($user, ['profile_id' => $userProfile->id]));
            }
        }
    }

    public function atravelUsers()
    {
        $commonsData = [
            'phone' => '987654321',
            'created_at' => now(),
            'updated_at' => now(),
            'password'   => '$2y$10$m5AyVB/UIm//9w5vBT1rgOYRlIM2JAcHBfNdbo/ZYWSPA/Y5qNOs6',
        ];

        $users = [
            [
                'name'       => 'Administrador',
                'email'      => 'bmsadmin@integer.pt',
                'profile'   => 'administrator'
            ],
            [
                'name'       => 'Atravel',
                'email'      => 'atravel@integer.pt',
                'profile'   => 'atravel'
            ]
        ];

        foreach ($users as $user) {
            $userProfile = DB::table('user_profile')->where('role', '=', $user['profile'])->first();

            unset($user['profile']);

            if (!DB::table('users')->where('name', $user['name'])->exists()) {
                DB::table('users')->insert(
                    array_merge($user, $commonsData, ['profile_id' => $userProfile->id])
                );
            } else {
                DB::table('users')->where('name', $user['name'])->update([
                    'password' => $commonsData['password'],
                    'updated_at' => $commonsData['updated_at']
                ]);
            }
        }
    }

    public function frutariaUsers()
    {
        if (!DB::table('users')->where('name', 'admin')->exists()) {
            $userProfile = DB::table('user_profile')->where('role', '=', 'admin')->first();

            DB::table('users')->insert([
                'name'       => 'admin',
                'email'      => 'bmsadmin@integer.pt',
                'password'   => '$2y$10$m5AyVB/UIm//9w5vBT1rgOYRlIM2JAcHBfNdbo/ZYWSPA/Y5qNOs6',
                'phone'      => '987654321',
                'created_at' => now(),
                'updated_at' => now(),
                'profile_id' => $userProfile->id
            ]);
        }
        if (!DB::table('users')->where('name', 'shipper')->exists()) {
            DB::table('users')->insert([
                'name'       => 'shipper',
                'email'      => 'bmsshipper@integer.pt',
                'password'   => '$2y$10$B8PH4cXYB6sgf8NNB2rMfuV1q9.DX71lPLRYl4WeGNvWvBQRKDZTe', //bcrypt('integerbms@ship...'),
                'phone'      => '987654321',
                'created_at' => now(),
                'updated_at' => now(),
                'profile_id' => '2'
            ]);
        }
        if (!DB::table('users')->where('name', 'attendant')->exists()) {
            DB::table('users')->insert([
                'name'       => 'attendant',
                'email'      => 'bmsattendant@integer.pt',
                'password'   => '$2y$10$Zxv2SgH0It0e45M311cu/uyTAQ.h3DDbk1Gd4nbigS3ynEa83QHA2', //bcrypt('integerbms@atte...'),
                'phone'      => '987654321',
                'created_at' => now(),
                'updated_at' => now(),
                'profile_id' => '3'
            ]);
        }
    }
}
