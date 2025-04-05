<?php

namespace App\Modules\Users\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Modules\Users\Models\UserProfileModules;

class UserProfileModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (env('BMS_CLIENT') == 'FRUTARIA') {
            $this->frutariaUserProfileModules();
        }

        if (env('BMS_CLIENT') == 'ASM') {
            $this->asmUserProfileModules();
        }

        if (env('BMS_CLIENT') == 'ATRAVEL') {
            $this->atravelUserProfileModules();
        }

        if (env('BMS_CLIENT') == 'UNI') {
            $this->uniUserProfileModules();
        }
    }

    public function uniUserProfileModules()
    {
        $profiles = DB::table('user_profile')->select('id', 'role')->get();

        $modulesProfile = [
            'admin' => [
                'dashboard' => ['dashboard_all:have', 'dashboard_personal:have'],
                'clients' => ['new_client:have', 'view_client:have', 'edit_client:have', 'delete_client:have'],
                'business' => ['new_business:have', 'view_business:have', 'edit_business:have', 'delete_business:have', 'new_follow_up:have'],
                'manager' => ['profiles:write', 'users:write', 'payments:write', 'administrative:write'],
                'products' => ['new_products:have', 'view_products:have', 'edit_products:have', 'delete_products:have']
            ],
            'referrer' => [
                'dashboard' => ['dashboard_all:have', 'dashboard_personal:have'],
                'clients' => ['new_client:have', 'view_client:have', 'edit_client:have', 'delete_client:have'],
                'business' => ['new_business:have', 'view_business:have', 'edit_business:have', 'delete_business:have', 'new_follow_up:have'],
                'manager' => [],
                'products' => ['new_products:have', 'view_products:have', 'edit_products:have', 'delete_products:have']
            ],
            'business_coach' => [
                'dashboard' => ['dashboard_all:have', 'dashboard_personal:have'],
                'clients' => ['new_client:have', 'view_client:have', 'edit_client:have', 'delete_client:have'],
                'business' => ['new_business:have', 'view_business:have', 'edit_business:have', 'delete_business:have', 'new_follow_up:have'],
                'manager' => [],
                'products' => ['new_products:have', 'view_products:have', 'edit_products:have', 'delete_products:have']
            ],
            'closer' => [
                'dashboard' => ['dashboard_all:have', 'dashboard_personal:have'],
                'clients' => ['new_client:have', 'view_client:have', 'edit_client:have', 'delete_client:have'],
                'business' => ['new_business:have', 'view_business:have', 'edit_business:have', 'delete_business:have', 'new_follow_up:have'],
                'manager' => [],
                'products' => ['new_products:have', 'view_products:have', 'edit_products:have', 'delete_products:have']
            ]
        ];

        $this->saveProfileModules($profiles, $modulesProfile);
    }

    public function asmUserProfileModules()
    {
        $profiles = DB::table('user_profile')->select('id', 'role')->get();

        $modulesProfile = [
            'admin' => [
                'dashboard' => ['dashboard:write'],
                'calls' => ['calls:write'],
                'users' => ['users:write'],
                'patients' => ['patients:write'],
                'clients' => ['clients:write'],
                'asm_schedule' => ['asm_schedule:write', 'asm_schedule_feedback:write', 'asm_schedule_canceled:write'],
            ],
            'coordinator' => [
                'dashboard' => ['dashboard:write'],
                'calls' => ['calls:write'],
                'users' => ['users:write'],
                'patients' => ['patients:write'],
                'clients' => ['clients:write'],
                'asm_schedule' => ['asm_schedule:write'],
            ],
            'accountancy' => [
                'dashboard' => ['dashboard:write'],
                'calls' => ['calls:write'],
                'users' => ['users:write'],
                'patients' => ['patients:write'],
                'clients' => ['clients:write'],
                'asm_schedule' => ['asm_schedule:write'],
            ],
            'tat' => [
                'dashboard' => ['dashboard:write'],
                'calls' => ['calls:write'],
                'users' => ['users:write'],
                'patients' => ['patients:write'],
                'clients' => ['clients:write'],
                'asm_schedule' => ['asm_schedule:write'],
            ]
        ];

        $this->saveProfileModules($profiles, $modulesProfile);
    }

    public function atravelUserProfileModules()
    {
        $profiles = DB::table('user_profile')->select('id', 'role')->get();

        $modulesProfile = [
            'administrator' => [
                'dashboard' => ['dashboard:write'],
                'bookings' => ['bookings:write', 'services:write', 'operators:none', 'bookingsToApprove:write', 'clients:write'],
                'timetable' => ['timetable:write'],
                'control' => ['operators:write', 'suppliers:write', 'staff:write', 'reportsStaff:write', 'reportsOperators:write', 'reportsSuppliers:write'],
                'tables' => ['operators:write', 'suppliers:write', 'staff:write'],
                'operators' => ['operators:write'],
                'suppliers' => ['suppliers:write'],
                'staff' => ['staff:write'],
                'vehicles' => ['vehicles:write'],
                'services' => ['serviceTypes:write', 'serviceStates:write'],
                'routes' => ['zones:write', 'routes:write', 'locations:write'],
                'users' => ['users:write', 'profilesAndPrivileges:write'],
                'companies' => ['companies:write'],
                'app' => ['app:write'],
                'recycling' => ['recycling:write']
            ],
            'atravel' => [
                'dashboard' => ['dashboard:write'],
                'bookings' => ['bookings:write', 'services:write', 'operators:none', 'bookingsToApprove:none', 'clients:write'],
                'timetable' => ['timetable:write'],
                'control' => ['operators:none', 'suppliers:write', 'staff:write', 'reportsStaff:write', 'reportsOperators:write', 'reportsSuppliers:write'],
                'tables' => ['operators:write', 'suppliers:write', 'staff:write'],
                'operators' => ['operators:write'],
                'suppliers' => ['suppliers:write'],
                'staff' => ['staff:write'],
                'vehicles' => ['vehicles:write'],
                'services' => ['serviceTypes:write', 'serviceStates:write'],
                'routes' => ['zones:write', 'routes:write', 'locations:write'],
                'users' => ['users:write', 'profilesAndPrivileges:write'],
                'companies' => ['companies:write'],
                'app' => ['app:write'],
                'recycling' => ['recycling:write']
            ],
            'operator' => [
                'dashboard' => ['dashboard:none'],
                'bookings' => ['bookings:none', 'services:none', 'operators:write', 'bookingsToApprove:none', 'clients:none'],
                'timetable' => ['timetable:none'],
                'control' => ['operators:none', 'suppliers:none', 'staff:none', 'reportsStaff:none', 'reportsOperators:none', 'reportsSuppliers:none'],
                'tables' => ['operators:none', 'suppliers:none', 'staff:none'],
                'operators' => ['operators:none'],
                'suppliers' => ['suppliers:none'],
                'staff' => ['staff:none'],
                'vehicles' => ['vehicles:none'],
                'services' => ['serviceTypes:none', 'serviceStates:none'],
                'routes' => ['zones:none', 'routes:none', 'locations:none'],
                'users' => ['users:none', 'profilesAndPrivileges:none'],
                'companies' => ['companies:none'],
                'app' => ['app:none'],
                'recycling' => ['recycling:none']
            ],
            'staff' => [
                'dashboard' => ['dashboard:none'],
                'bookings' => ['bookings:none', 'services:none', 'operators:none', 'bookingsToApprove:none', 'clients:none'],
                'timetable' => ['timetable:read'],
                'control' => ['operators:none', 'suppliers:none', 'staff:none', 'reportsStaff:none', 'reportsOperators:none', 'reportsSuppliers:none'],
                'tables' => ['operators:none', 'suppliers:none', 'staff:none'],
                'operators' => ['operators:none'],
                'suppliers' => ['suppliers:none'],
                'staff' => ['staff:none'],
                'vehicles' => ['vehicles:none'],
                'services' => ['serviceTypes:none', 'serviceStates:none'],
                'routes' => ['zones:none', 'routes:none', 'locations:none'],
                'users' => ['users:none', 'profilesAndPrivileges:none'],
                'companies' => ['companies:none'],
                'app' => ['app:read'],
                'recycling' => ['recycling:none']
            ]
        ];

        $this->saveProfileModules($profiles, $modulesProfile);
    }

    public function saveProfileModules($profiles, $modulesProfile)
    {
        foreach ($profiles as $profile) {
            if (UserProfileModules::where('profile_id', $profile->id)->exists()) {
                if (isset($modulesProfile[$profile->role])) {
                    foreach ($modulesProfile[$profile->role] as $module => $permissions) {
                        $permissionsArray = [];

                        foreach ($permissions as $permission) {
                            [$key, $value] = explode(':', $permission);

                            $permissionsArray[$key] = $value;
                        }

                        UserProfileModules::where([
                            'profile_id'  => $profile->id,
                            'module' => $module
                        ])->update([
                            'permissions' => $permissionsArray
                        ]);
                    }
                }

                continue;
            }

            if (isset($modulesProfile[$profile->role])) {
                foreach ($modulesProfile[$profile->role] as $module => $permissions) {
                    $permissionsArray = [];

                    foreach ($permissions as $permission) {
                        [$key, $value] = explode(':', $permission);

                        $permissionsArray[$key] = $value;
                    }

                    UserProfileModules::create([
                        'profile_id'  => $profile->id,
                        'module' => $module,
                        'permissions' => $permissionsArray
                    ]);
                }
            }
        }
    }

    public function frutariaUserProfileModules()
    {
        if (!DB::table('user_profile_modules')->where('profile_id', 1)->exists()) {
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 1,
                'module' => 'homepage',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 1,
                'module' => 'calls',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 1,
                'module' => 'order',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 1,
                'module' => 'products',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 1,
                'module' => 'orders-tracking',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 1,
                'module' => 'scheduling',
            ]);
        }

        if (!DB::table('user_profile_modules')->where('profile_id', 3)->exists()) {
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 3,
                'module' => 'homepage',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 3,
                'module' => 'calls',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 3,
                'module' => 'order',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 3,
                'module' => 'orders-tracking',
            ]);
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 3,
                'module' => 'scheduling',
            ]);
        }

        if (!DB::table('user_profile_modules')->where('profile_id', 2)->exists()) {
            DB::table('user_profile_modules')->insert([
                'profile_id'  => 2,
                'module' => 'orders-tracking',
                'permissions' => json_encode([
                    'pending' => true,
                    'preparation' => true,
                    'delivery' => true
                ]),
            ]);
        }
    }
}
