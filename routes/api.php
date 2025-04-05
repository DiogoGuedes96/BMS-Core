<?php

use App\Events\TesteEvent;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    return response()->json([
        'project' => 'New bms project API.',
        'version' => '0.0.1',
        'updated' => '2023-07-18'
    ]);
});

if (config('modules.Users')) {
    Route::prefix('users')->group(function () {
        include base_path('app/Modules/Users/Routes/api.php');
    });
}

if (config('modules.Clients')) {
    Route::prefix('clients')->group(function () {
        include base_path('app/Modules/Clients/Routes/api.php');
    });
}

if (config('modules.Products')) {
    Route::prefix('products')->group(function () {
        include base_path('app/Modules/Products/Routes/api.php');
    });
}

if (config('modules.Primavera')) {
    Route::prefix('primavera')->group(function () {
        include base_path('app/Modules/Primavera/Routes/api.php');
    });
}

if (config('modules.Orders')) {
    Route::prefix('orders')->group(function () {
        include base_path('app/Modules/Orders/Routes/api.php');
    });
}

if (config('modules.Calls')) {
    Route::prefix('calls')->group(function () {
        include base_path('app/Modules/Calls/Routes/api.php');
    });
}

if (config('modules.Dashboard')) {
    Route::prefix('dashboard')->group(function () {
        include base_path('app/Modules/Dashboard/Routes/api.php');
    });
}

if (config('modules.Schedule')) {
    Route::prefix('schedule')->group(function () {
        include base_path('app/Modules/Schedule/Routes/api.php');
    });
}

if (config('modules.Patients')) {
    Route::prefix('patients')->group(function () {
        include base_path('app/Modules/Patients/Routes/api.php');
    });
}

if (config('modules.Feedback')) {
    Route::prefix('feedbacks')->group(function () {
        include base_path('app/Modules/Feedback/Routes/api.php');
    });
}

if (config('modules.ServiceScheduling')) {
    Route::prefix('scheduling')->group(function () {
        include base_path('app/Modules/ServiceScheduling/Routes/api.php');
    });
}

if (config('modules.Routes')) {
    Route::prefix('routes')->group(function () {
        include base_path('app/Modules/Routes/Routes/api.php');
    });
}

if (config('modules.Tables')) {
    Route::prefix('tables')->group(function () {
        include base_path('app/Modules/Tables/Routes/api.php');
    });
}

Route::prefix('feedback')->group(function () {
    if (config('modules.feedback')) {
        include base_path('app/Modules/Feedback/Routes/api.php');
    }
});

if (config('modules.ServiceScheduling')) {
    Route::prefix('servicescheduling')->group(function () {
        include base_path('app/Modules/ServiceScheduling/Routes/api.php');
    });
}

if (config('modules.Workers')) {
    Route::prefix('workers')->group(function () {
        include base_path('app/Modules/Workers/Routes/api.php');
    });
}

if (config('modules.Bookings')) {
    Route::prefix('bookings')->group(function () {
        include base_path('app/Modules/Bookings/Routes/api.php');
    });
}

if (config('modules.Vehicles')) {
    Route::prefix('vehicles')->group(function () {
        include base_path('app/Modules/Vehicles/Routes/api.php');
    });
}

if (config('modules.Services')) {
    Route::prefix('services')->group(function () {
        include base_path('app/Modules/Services/Routes/api.php');
    });
}

if (config('modules.AmbulanceCrew')) {
    Route::prefix('ambulance-crew')->group(function () {
        include base_path('app/Modules/AmbulanceCrew/Routes/api.php');
    });
}

if (config('modules.UniClients')) {
    Route::prefix('uniclients')->group(function () {
        include base_path('app/Modules/UniClients/Routes/api.php');
    });
}

if (config('modules.Notification')) {
    Route::prefix('notifications')->group(function () {
        include base_path('app/Modules/Notification/Routes/api.php');
    });
}

if (config('modules.Companies')) {
    Route::prefix('companies')->group(function () {
        include base_path('app/Modules/Companies/Routes/api.php');
    });
}

Route::get('/message', [NotificationController::class, 'message']);

if (config('modules.products')) {
    Route::prefix('products')->group(function () {
        include base_path('app/Modules/Products/Routes/api.php');
    });
}

if (config('modules.Business')) {
    Route::prefix('business')->group(function () {
        include base_path('app/Modules/Business/Routes/api.php');
    });
}

if (config('modules.UniDashboard')) {
    Route::prefix('unidashboard')->group(function () {
        include base_path('app/Modules/UniDashboard/Routes/api.php');
    });
}

Route::prefix('activecampaign')->group(function () {

if (config('modules.activecampaign')) {
    include base_path('app/Modules/ActiveCampaign/Routes/api.php');
}
});
