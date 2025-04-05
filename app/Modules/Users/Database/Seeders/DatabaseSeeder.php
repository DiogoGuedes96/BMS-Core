<?php

namespace App\Modules\Users\Database\Seeders;

use Illuminate\Database\Seeder;

use App\Modules\Business\Models\Business;
use App\Modules\Business\Models\BusinessFollowup;
use App\Modules\Business\Models\BusinessKanban;
use App\Modules\Business\Models\BusinessKanbanColumns;
use App\Modules\Business\Models\BusinessNotes;
use App\Modules\Business\Models\BusinessPaymentsResponsible;
use App\Modules\Business\Models\KanbanHistory;
use App\Modules\Notification\Models\Notifications;
use App\Modules\UniClients\Models\ReferrerChangeRequest;
use App\Modules\UniClients\Models\UniClients;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\UserProfile;
use App\Modules\Users\Models\UserProfileModules;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserProfileSeeder::class,
            ModulePermissionsSeeder::class,
            UserProfileModulesSeeder::class,
            UsersSeeder::class
        ]);
    }
}
