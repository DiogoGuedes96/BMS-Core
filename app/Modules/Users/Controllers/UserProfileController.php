<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Users\Resources\UserProfileResource;
use App\Modules\Users\Resources\UserProfileModuleResource;
use App\Modules\Users\Requests\UserProfileRequest;
use App\Modules\Users\Requests\UserModulePermissionsRequest;
use App\Modules\Users\Services\UserProfileService;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function __construct(
        private UserProfileService $userProfileService
    ) {
    }

    public function index(Request $request)
    {
        $profiles = $this->userProfileService->listAll($request);

        return (UserProfileResource::collection($profiles))
            ->response()->setStatusCode(200);
    }

    public function store(UserProfileRequest $request)
    {
        $result = $this->userProfileService->create($request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content,
            ], 400);
        }

        return (new UserProfileResource($result->content))
            ->response()->setStatusCode(201);
    }

    private function getUserProfile($idOrRole)
    {
        if (is_numeric($idOrRole)) {
            return $this->userProfileService->getById($idOrRole);
        } else {
            return $this->userProfileService->getByRole($idOrRole);
        }
    }

    public function show($idOrRole)
    {
        $userProfile = $this->getUserProfile($idOrRole);

        if (!$userProfile) {
            return response()->json([
                'message' => 'Registro não encontrado.'
            ], 404);
        }

        return (new UserProfileModuleResource($userProfile))
            ->response()->setStatusCode(200);
    }

    public function update(UserProfileRequest $request, $idOrRole)
    {
        $userProfile = $this->getUserProfile($idOrRole);

        if (!$userProfile) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $result = $this->userProfileService->update($userProfile, $request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return (new UserProfileResource($result->content))
            ->response()->setStatusCode(200);
    }

    public function syncPermissions(UserModulePermissionsRequest $request, $idOrRole)
    {
        $userProfile = $this->getUserProfile($idOrRole);

        if (!$userProfile) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $result = $this->userProfileService->syncPermissions($userProfile, $request->module_permissions);

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return (new UserProfileModuleResource($userProfile))
            ->response()->setStatusCode(200);
    }

    public function destroy($idOrRole)
    {
        $userProfile = $this->getUserProfile($idOrRole);

        if (!$userProfile) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        if ($userProfile->readonly) {
            return response()->json([
                'message' => 'O perfil "' . $userProfile->description . '" não pode ser excluído.'
            ], 400);
        }

        $associatedData = [];

        if ($userProfile->users->count() > 0) {
            $associatedData[] = 'utilizadores';
        }

        if (!empty($associatedData)) {
            return response()->json([
                'message' => 'O perfil "' . $userProfile->description . '" não pode ser excluído, pois está vinculado a ' . implode(' e ', $associatedData) . '.'
            ], 400);
        }

        $deletedUsers = $userProfile->users()->onlyTrashed()->get();
        $deletedUsers->each(function ($user) {
            $user->update(['profile_id' => null]);
        });

        $result = $this->userProfileService->delete($userProfile);

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return response()->json([], 204);
    }

    public function getUserProfilesActive() {
        $result = $this->userProfileService->getUserProfilesActive();

        if (empty($result)) {
            return response()->json([
                'message' => 'Não foi encontrado nenhum resgisto ativo'
            ], 404);
        }

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }
        return response()->json($result->content, 200);
    }
}
