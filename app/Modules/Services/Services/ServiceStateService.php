<?php

namespace App\Modules\Services\Services;

use Illuminate\Support\Facades\DB;
use App\Modules\Services\Models\ServiceState;
use App\Services\Service;

class ServiceStateService extends Service
{
    public function getAll($request)
    {
        $order = $request->order ?? 'name-asc';

        [$field, $direction] = explode('-', $order);

        $serviceStates = ServiceState::orderBy($field, $direction);

        if ($request->filled('search')) {
            $serviceStates->where('name', 'like', '%'. $request->search .'%');
        }

        $serviceStates = ($request->has('page'))
			? $serviceStates->paginate((int) $request->per_page ?? 10)
			: $serviceStates->get();

        return $serviceStates;
    }

    public function getById($id)
    {
        return ServiceState::find($id);
    }

    public function getByNameInTrash($name)
    {
        return ServiceState::withTrashed()->where('name', '=', $name)->first();
    }

    public function restoreFromTrash(ServiceState $serviceState, $request)
    {
        $id = $serviceState->id;
        $serviceState->restore();

        $serviceState = $this->getById($id);
        $serviceState->update($request);

        return $serviceState;
    }

    public function create($request)
    {
        DB::beginTransaction();

        try {
            if (!isset($request['active'])) {
                $request['active'] = true;
            }

            if (!isset($request['is_default'])) {
                $request['is_default'] = false;
            }

            if (!$serviceState = ServiceState::create($request)) {
                throw new \Exception('Houve um erro ao tentar guardar o registro.');
            }

            DB::commit();

            return $this->result($serviceState, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function update(ServiceState $serviceState, $request)
    {
        DB::beginTransaction();

        try {
            if (!$serviceState->update($request)) {
                throw new \Exception('Houve um erro ao tentar atualizar o registro.');
            }

            DB::commit();

            return $this->result($serviceState, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function resetDefault()
    {
        DB::beginTransaction();

        try {
            ServiceState::where('is_default', true)->update(['is_default' => false]);

            DB::commit();

            return $this->result([], true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function delete(ServiceState $serviceState)
    {
        DB::beginTransaction();

        try {
            if (!$serviceState->delete()) {
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
