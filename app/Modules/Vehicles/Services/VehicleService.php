<?php

namespace App\Modules\Vehicles\Services;

use Illuminate\Support\Facades\DB;
use App\Modules\Vehicles\Models\Vehicle;
use App\Services\Service;

class VehicleService extends Service
{
    const SEARCHABLE_FIELDS = [
        'brand',
        'model',
        'license',
        'km',
    ];

    public function getAll($request)
    {
        $order = $request->order ?? 'id-asc';

        [$field, $direction] = explode('-', $order);

        $vehicles = Vehicle::orderBy($field, $direction);

        if ($request->filled('search')) {
            $search = $request->search;

            $vehicles->where(function($query) use ($search) {
                foreach(self::SEARCHABLE_FIELDS as $key => $field) {
                    if ($key > 0) {
                        $query->orWhere($field, 'like', '%'. $search .'%');
                    } else {
                        $query->where($field, 'like', '%'. $search .'%');
                    }
                }
            });
        }

        if ($request->filled('group')) {
            $vehicles->where('group', '=', $request->group);
        }

        $vehicles = ($request->has('page'))
			? $vehicles->paginate((int) $request->per_page ?? 10)
			: $vehicles->get();

        return $vehicles;
    }

    public function getById($id)
    {
        return Vehicle::find($id);
    }

    public function create($request)
    {
        DB::beginTransaction();

        try {
            if (!isset($request['active'])) {
                $request['active'] = true;
            }

            if (!$vehicle = Vehicle::create($request)) {
                throw new \Exception('Houve um erro ao tentar guardar o registro.');
            }

            DB::commit();

            return $this->result($vehicle, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function update(Vehicle $vehicle, $request)
    {
        DB::beginTransaction();

        try {
            if (!$vehicle->update($request)) {
                throw new \Exception('Houve um erro ao tentar atualizar o registro.');
            }

            DB::commit();

            return $this->result($vehicle, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function delete(Vehicle $vehicle)
    {
        DB::beginTransaction();

        try {
            if (!$vehicle->delete()) {
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
