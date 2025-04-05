<?php

namespace App\Modules\Users\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use App\Modules\Users\Models\User;
use App\Modules\Users\Requests\LoginRequest;
use App\Modules\Users\Requests\ForgotPasswordRequest;
use App\Modules\Users\Requests\ResetPasswordRequest;
use App\Modules\Users\Services\UsersService;

class AuthController extends Controller
{
    public function __construct(private UsersService $userService)
    {
    }

    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                $token = $user->createToken('api-token')->plainTextToken;

                $profileModules = $user->profile->userProfileModules;

                $modulesCanAccess = [];

                foreach ($profileModules as $module) {
                    array_push($modulesCanAccess, [
                        "module" => $module->module,
                        "permissions" => $module->permissions
                    ]);
                }

                $firstAccess = false;
                if (empty($user->first_access)) {
                    $firstAccess = true;
                }

                $this->userService->updateLoginDate($user);

                return response()->json([
                    'result' => 'success',
                    'success' => true,
                    'user' => [
                        'token' => $token,
                        'id'    => $user->id,
                        'name'  => $user->name,
                        'email' => $user->email,
                        'canAccess' => $modulesCanAccess,
                        'role' => $user->profile->role,
                        'first_access' => $firstAccess
                    ]
                ]);
            }

            return response()->json(['message' => 'Email ou password inválidos'], 401);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function logout(Request $request)
    {
        try {
            $this->userService->logout($request->user());

            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        $code = [
            Password::RESET_LINK_SENT => 200,
            Password::INVALID_USER => 404
        ];

        return response()->json([
            'message' => __($status)
        ], $code[$status] ?? 400);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        $code = [
            Password::PASSWORD_RESET => 200,
            Password::INVALID_USER => 404
        ];

        return response()->json([
            'message' => __($status)
        ], $code[$status] ?? 400);
    }
}
