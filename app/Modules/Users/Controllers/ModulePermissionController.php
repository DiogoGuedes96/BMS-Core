<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Users\Resources\ModulePermissionResource;
use App\Modules\Users\Services\ModulePermissionService;
use Illuminate\Http\Request;

class ModulePermissionController extends Controller
{
    public function __construct(
        private ModulePermissionService $modulePermissionService
    ) {
    }

    public function index(Request $request)
    {
        $modulePermissions = $this->modulePermissionService->listAll($request);

        return (ModulePermissionResource::collection($modulePermissions))
            ->response()->setStatusCode(200);
    }
}
