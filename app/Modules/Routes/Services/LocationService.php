<?php

namespace App\Modules\Routes\Services;

use App\Modules\Routes\Models\Location;
use Illuminate\Support\Facades\DB;
use App\Modules\Routes\Enums\StatusEnum;
use App\Services\Service;

class LocationService extends Service
{
    const SEARCHABLE_FIELDS = [
        'name',
        'address'
    ];

    public function getAll($request)
    {
        $order = $request->order ?? 'name-asc';

        [$field, $direction] = explode('-', $order);

        $locations = Location::orderBy($field, $direction);

        if ($request->filled('search')) {
            $search = $request->search;

            $locations->where(function($query) use ($search) {
                foreach(self::SEARCHABLE_FIELDS as $key => $field) {
                    if ($key > 0) {
                        $query->orWhere($field, 'like', '%'. $search .'%');
                    } else {
                        $query->where($field, 'like', '%'. $search .'%');
                    }
                }
            });
        }

        if ($request->filled('zone_id')) {
            $locations->where('zone_id', '=', $request->zone_id);
        }

        if ($request->filled('status')) {
            $locations->where('active', '=', StatusEnum::getAll()[$request->status]);
        }

        $locations = ($request->has('page'))
			? $locations->paginate((int) $request->per_page ?? 10)
			: $locations->get();

        return $locations;
    }

    public function getById($id)
    {
        return Location::find($id);
    }

    public function create($request)
    {
        DB::beginTransaction();

        try {
            if (!isset($request['active'])) {
                $request['active'] = true;
            }

            if (!$location = Location::create($request)) {
                throw new \Exception('Houve um erro ao tentar guardar o registro.');
            }

            DB::commit();

            return $this->result($location, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function update(Location $location, $request)
    {
        DB::beginTransaction();

        try {
            if (!$location->update($request)) {
                throw new \Exception('Houve um erro ao tentar atualizar o registro.');
            }

            DB::commit();

            return $this->result($location, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function delete(Location $location)
    {
        DB::beginTransaction();

        try {
            if (!$location->delete()) {
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
