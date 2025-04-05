<?php

namespace App\Modules\Routes\Services;

use App\Modules\Routes\Models\Route;
use Illuminate\Support\Facades\DB;
use App\Services\Service;

class RoutesService extends Service
{
    public function create($request)
    {
        $route = new Route();
        $route->from_zone_id = $request->from_zone_id;
        $route->to_zone_id = $request->to_zone_id;
        $route->save();

        return $route;
    }

    public function edit($request, $id)
    {
        $route = Route::find($id);
        $route->from_zone_id = $request->from_zone_id;
        $route->to_zone_id = $request->to_zone_id;
        $route->save();

        return $route;
    }

    public function get($id)
    {
        return Route::find($id);
    }

    public function all($params)
    {
        $routes = Route::with('fromZone:id,name')
            ->with('toZone:id,name');

        if ($params->has('search')) {
            $routes->whereHas('fromZone', function ($query) use ($params) {
                $query->where('name', 'like', '%' . $params->search . '%');
            })->orWhereHas('toZone', function ($query) use ($params) {
                $query->where('name', 'like', '%' . $params->search . '%');
            });
        }

        if ($params->has('sorter')) {
            $sorter = $params->sorter === 'ascend' ? 'asc' : 'desc';
            $routes->join('bms_zones as from_zones', 'from_zones.id', '=', 'bms_routes.from_zone_id')
                ->orderBy('from_zones.name', $sorter);
        }

        $routes = ($params->has('page'))
			? $routes->paginate((int) $params->per_page ?? 10)
			: $routes->get();

        return $routes;
    }

    public function delete(Route $route)
    {
        DB::beginTransaction();

        try {
            if (!$route->delete()) {
                throw new \Exception('Houve um erro ao tentar excluir o registro.');
            }

            DB::commit();

            return $this->result('Registro removido com sucesso', true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function getByZonesInTrash($from_zone_id, $to_zone_id)
    {
        return Route::withTrashed()
            ->where('from_zone_id', '=', $from_zone_id)
            ->where('to_zone_id', '=', $to_zone_id)
            ->first();
    }

    public function restoreFromTrash(Route $route)
    {
        $id = $route->id;
        $route->restore();

        return $this->get($id);
    }
}
