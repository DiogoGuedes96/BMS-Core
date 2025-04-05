<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Business\Services\BusinessService;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\UserProfile;
use App\Modules\Users\Requests\ChangePasswordRequest;
use App\Modules\Users\Requests\CreateUserRequest;
use App\Modules\Users\Requests\UpdateUserRequest;
use App\Modules\Users\Resources\UserResource;
use App\Modules\Users\Resources\UserTotalResource;
use App\Modules\Users\Services\UsersService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function __construct(
        private UsersService $userService,
        private BusinessService $businessService
    ) {
    }

    public function index(Request $request, string $role = null)
    {
        try {
            $users = $this->userService->list($request, $role);

            return (UserResource::collection($users))
                ->response()->setStatusCode(200);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function show($id)
    {
        if (!$user = $this->userService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado.'
            ], 404);
        }

        return (new UserResource($user))
            ->response()->setStatusCode(200);
    }

    public function total()
    {
        $total = $this->userService->getTotal();

        return (new UserTotalResource($total))
            ->response()->setStatusCode(200);
    }

    public function store(CreateUserRequest $request)
    {
        try {
            $user = $this->userService->createUser($request);
            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = $this->userService->updateUser($request, $id);

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function inactive($id)
    {
        try {
            $this->userService->inactivate($id);

            return response()->json(['message' => 'User inactivated']);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function forceChangePassword(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$this->userService->forceChangePassword($request, $user)) {
                return response()->json(['message' => 'Erro ao atualizar a palavra-passe.'], 400);
            }

            return response()->json(['message' => 'Atualizado a palavra-passe do utilizador.'], 201);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = auth()->user();

            if (!$this->userService->changePassword($request, $user)) {
                return response()->json(['message' => 'A password atual está incorreta.'], 400);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id)
    {
        if (env('BMS_CLIENT') === 'UNI') {
            try {
                $user = $this->userService->getById($id);
                $userProfileModel = new UserProfile();
                $profileAdmin = $userProfileModel->where('role', 'admin')->first();

                $userAdmin = new User();
                $userAdmin = $userAdmin
                    ->where('profile_id', $profileAdmin->id)
                    ->where('id', '!=', $id)
                    ->first();

                if (empty($userAdmin)) {
                    throw new Exception("Você precisa de um usuário de perfil admin ativo para excluir outros usuários.", 1);
                }

                $this->businessService->changeResponsiblesToAdmin($user, $userAdmin);

                $user->delete();

                return response()->json([
                    'message' => 'Utilizador excluído com sucesso.'
                ], 200);
            } catch (\Throwable $th) {
                return response()->json([
                    'message' => 'Não foi possivel excluir o utilizador.'
                ], 400);
            }
        }

        if (!$user = $this->userService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $associatedData = [];

        if (!empty($user->worker)) {
            $label = [
                'operators' => 'Operador ',
                'staff' => 'Staff '
            ];

            $associatedData[] = '\'' . $label[$user->worker->type] . $user->worker->name . '\'';
        }

        if (!empty($associatedData)) {
            return response()->json([
                'message' => 'O utilizador "' . $user->name . '" não pode ser excluído, pois está vinculado a ' . implode(' e ', $associatedData) . '.'
            ], 400);
        }

        $result = $this->userService->delete($user);

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return response()->json([], 204);
    }

    public function hardDelete(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $user = $this->userService->getById($id);

            $userProfileModel = new UserProfile();
            $profileAdmin = $userProfileModel->where('role', 'admin')->first();

            $userAdmin = new User();
            $userAdmin = $userAdmin
                ->where('profile_id', $profileAdmin->id)
                ->where('id', '!=', $id)
                ->first();

            if (empty($userAdmin)) {
                throw new Exception("Você precisa de um usuário de perfil admin ativo para excluir outros usuários.", 1);
            }

            $this->businessService->changeResponsiblesToAdmin($user, $userAdmin);

            $this->userService->hardDelete($id);

            DB::commit();

            return response()->json([
                'message' => $id
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'Erro ao deletar cliente',
            ], 404);
        }
    }
}
