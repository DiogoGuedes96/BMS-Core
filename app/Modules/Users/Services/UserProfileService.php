<?php

namespace App\Modules\Users\Services;

use App\Modules\Users\Models\ModulePermission;
use App\Modules\Users\Models\UserProfile;
use App\Modules\Users\Models\UserProfileModules;
use App\Modules\Users\Helpers\TransformFieldsHelper;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

class UserProfileService extends Service
{
    public function listAll($request)
    {
        $search = $request->search ?? null;
        $order = $request->order ?? 'description-asc';

        [$field, $direction] = explode('-', $order);

        $userProfile = UserProfile::orderBy($field, $direction);

        if (!empty($search)) {
            $userProfile->where('description', 'like', "%$search%");
        }

        return $userProfile->paginate($request->per_page ?? 10);
    }

    public function listModulePermissions($active = null, $module = null)
    {
        $modulePermissions = ModulePermission::orderBy('id', 'asc');

        if (!empty($module)) {
            $modulePermissions->where('module', '=', $module);
        }

        if (!empty($active)) {
            $modulePermissions->where('active', '=', $active);
        }

        return $modulePermissions->get();
    }

    public function getById($id)
    {
        return UserProfile::find($id);
    }

    public function getByRole($role)
    {
        return UserProfile::where('role', '=', $role)->first();
    }

    public function create($request)
    {
        DB::beginTransaction();

        try {
            $request['role'] = TransformFieldsHelper::alias($request['description']);

            if (!$userProfile = UserProfile::create($request)) {
                throw new \Exception('Houve um erro ao tentar guardar o registro.');
            }

            DB::commit();

            return $this->result($userProfile, true);
        } catch (\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function update(UserProfile $userProfile, $request)
    {
        DB::beginTransaction();

        try {
            $request['role'] = TransformFieldsHelper::alias($request['description']);

            if (!$userProfile->update($request)) {
                throw new \Exception('Houve um erro ao tentar atualizar o registro.');
            }

            DB::commit();

            return $this->result($userProfile, true);
        } catch (\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function delete(UserProfile $userProfile)
    {
        DB::beginTransaction();

        try {
            if (!$userProfile->delete()) {
                throw new \Exception('Houve um erro ao tentar excluir o registro.');
            }

            DB::commit();

            return $this->result('Registro removido com sucesso', true);
        } catch (\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function syncPermissions(UserProfile $userProfile, $modulePermissions)
    {
        DB::beginTransaction();

        try {
            foreach ($modulePermissions as $modulePermission) {
                UserProfileModules::updateOrCreate(
                    ['module' => $modulePermission['module'], 'profile_id' => $userProfile->id],
                    ['permissions' => $modulePermission['permissions']]
                );
            }

            DB::commit();

            return $this->result($userProfile, true);
        } catch (\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function getUserProfilesActive() {
        DB::beginTransaction();

        try {
            $usersProfile = UserProfile::active()->get();
            
            DB::commit();

            return $this->result($usersProfile, true);
        } catch (\Exception $error) {
            DB::rollBack();
            return $this->result($error->getMessage(), false);
        }
    }
}
