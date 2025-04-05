<?php

namespace App\Modules\Users\Services;

use App\Modules\Users\Models\ModulePermission;
use App\Services\Service;
use App\Modules\Users\Enums\StatusEnum;
use Illuminate\Support\Facades\DB;

class ModulePermissionService extends Service
{
    public function listAll($request)
    {
        $modulePermissions = ModulePermission::orderBy('id', 'asc');

        if ($request->filled('module')) {
            $modulePermissions->where('module', '=', $request->module);
        }

        if ($request->filled('status')) {
            $modulePermissions->where('active', '=', StatusEnum::getAll()[$request->status]);
        }

        return $modulePermissions->get();
    }

    public function getById($id)
    {
        return ModulePermission::find($id);
    }

    public function getByModule($module)
    {
        return ModulePermission::where('module', '=', $module)->first();
    }

    public function create($request)
    {
        DB::beginTransaction();

        try {
            if (!$modulePermission = ModulePermission::create($request)) {
                throw new \Exception('Houve um erro ao tentar guardar o registro.');
            }

            DB::commit();

            return $this->result($modulePermission, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function update(ModulePermission $modulePermission, $request)
    {
        DB::beginTransaction();

        try {
            if (!$modulePermission->update($request)) {
                throw new \Exception('Houve um erro ao tentar atualizar o registro.');
            }

            DB::commit();

            return $this->result($modulePermission, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function delete(ModulePermission $modulePermission)
    {
        DB::beginTransaction();

        try {
            if (!$modulePermission->delete()) {
                throw new \Exception('Houve um erro ao tentar excluir o registro.');
            }

            DB::commit();

            return $this->result('Registro removido com sucesso', true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }
}
