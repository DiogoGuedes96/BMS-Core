<?php

namespace App\Modules\Routes\Services;

use App\Modules\Routes\Models\Zone;
use Illuminate\Support\Facades\DB;
use App\Services\Service;

class ZonesService extends Service
{
    public function create($request)
    {
        $zone = new Zone();
        $zone->name = $request->name;
        $zone->save();

        return $zone;
    }

    public function edit($request, $id)
    {
        $zone = Zone::find($id);
        $zone->name = $request->name;
        $zone->save();

        return $zone;
    }

    public function get($id)
    {
        $zone = Zone::find($id);

        return $zone;
    }

    public function all()
    {
        return Zone::orderBy('name', 'asc')->get();
    }

    public function paginate($params)
    {
        $sorter = !$params->has('sorter') || $params->sorter === 'ascend'
            ? 'asc' : 'desc';

        $zones = Zone::orderBy('name', $sorter);

        if ($params->has('search')) {
            $zones->where('name', 'like', '%' . $params->search . '%');
        }

        $perPage = $params->has('per_page') ? $params->per_page : 10;

        $zones = $zones->paginate($perPage);

        return $zones;
    }

    public function delete(Zone $zone)
    {
        DB::beginTransaction();

        try {
            if (!$zone->delete()) {
                throw new \Exception('Houve um erro ao tentar excluir o registro.');
            }

            DB::commit();

            return $this->result('Registro removido com sucesso', true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function getByNameInTrash($name)
    {
        return Zone::withTrashed()->where('name', '=', $name)->first();
    }

    public function restoreFromTrash(Zone $zone)
    {
        $id = $zone->id;
        $zone->restore();

        return $this->get($id);
    }
}
