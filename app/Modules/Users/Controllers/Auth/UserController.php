<?php

namespace App\Modules\Users\Controllers\Auth;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getUser()
    {
        $user  = auth()->user();
        $token = $user->createToken('api-token')->plainTextToken;

        $profileModules = $user->profile->userProfileModules;

        $modulesCanAccess = [];

        foreach ($profileModules as $module) {
            array_push($modulesCanAccess, [
                "module" => $module->module,
                "permissions" => $module->permissions
            ]);
        }

        return json_encode([
            'result' => 'success',
            'success' => true,
            'user' => [
                'name'  => auth()->user()->name,
                'email' => auth()->user()->email,
                'token' => $token,
                'canAccess' => $modulesCanAccess
            ]
        ]);
    }
}
