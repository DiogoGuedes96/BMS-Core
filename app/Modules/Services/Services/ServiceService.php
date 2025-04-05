<?php

namespace App\Modules\Services\Services;

use Illuminate\Support\Facades\DB;
use App\Modules\Services\Models\Service;
use App\Services\Service as ParentService;
use App\Modules\Services\Enums\ColorsEnum;

class ServiceService extends ParentService
{
    public function getAll($request)
    {
        $order = $request->order ?? 'name-asc';

        [$field, $direction] = explode('-', $order);

        $services = Service::orderBy($field, $direction);

        if ($request->filled('search')) {
            $services->where('name', 'like', '%'. $request->search .'%');
        }

        $services = ($request->has('page'))
			? $services->paginate((int) $request->per_page ?? 10)
			: $services->get();

        return $services;
    }

    public function getById($id)
    {
        return Service::find($id);
    }

    public function getByNameInTrash($name)
    {
        return Service::withTrashed()->where('name', '=', $name)->first();
    }

    public function restoreFromTrash(Service $service, $request)
    {
        $id = $service->id;
        $service->restore();

        $service = $this->getById($id);
        $service->update($request);

        return $service;
    }

    public function create($request)
    {
        DB::beginTransaction();

        try {
            if (!isset($request['active'])) {
                $request['active'] = true;
            }

            if (!isset($request['color'])) {
                $request['color'] = ColorsEnum::NO_COLOR;
            }

            if (!$service = Service::create($request)) {
                throw new \Exception('Houve um erro ao tentar guardar o registro.');
            }

            DB::commit();

            return $this->result($service, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function update(Service $service, $request)
    {
        DB::beginTransaction();

        try {
            if (!$service->update($request)) {
                throw new \Exception('Houve um erro ao tentar atualizar o registro.');
            }

            DB::commit();

            return $this->result($service, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function delete(Service $service)
    {
        DB::beginTransaction();

        try {
            if (!$service->delete()) {
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
