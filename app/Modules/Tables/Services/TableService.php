<?php

namespace App\Modules\Tables\Services;

use App\Services\Service;
use App\Modules\Tables\Models\Table;
use Illuminate\Support\Facades\DB;
use App\Modules\Tables\Enums\StatusEnum;

class TableService extends Service
{
    public function getAll($request)
    {
        $order = $request->order ?? 'name-asc';

        [$field, $direction] = explode('-', $order);

        $tables = Table::orderBy($field, $direction);

        if ($request->filled('search')) {
            $tables->where('name', 'like', '%'. $request->search .'%');
        }

        if ($request->filled('type')) {
            $tables->where('type', '=', $request->type);
        }

        if ($request->filled('status')) {
            $tables->where('active', '=', StatusEnum::getAll()[$request->status]);
        }

        $tables = ($request->has('page'))
			? $tables->paginate((int) $request->per_page ?? 10)
			: $tables->get();

        return $tables;
    }

    public function getIdsByType($type)
    {
        return Table::where('type', '=', $type)
            ->where('active', true)
            ->pluck('id');
    }

    public function getById($id)
    {
        return Table::find($id);
    }

    public function create($request)
    {
        DB::beginTransaction();

        try {
            if (!isset($request['active'])) {
                $request['active'] = true;
            }

            if (!$table = Table::create($request)) {
                throw new \Exception('Houve um erro ao tentar guardar o registro.');
            }

            DB::commit();

            return $this->result($table, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function update(Table $table, $request)
    {
        DB::beginTransaction();

        try {
            if (!$table->update($request)) {
                throw new \Exception('Houve um erro ao tentar atualizar o registro.');
            }

            DB::commit();

            return $this->result($table, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function delete(Table $table)
    {
        DB::beginTransaction();

        try {
            if (!$table->delete()) {
                throw new \Exception('Houve um erro ao tentar excluir o registro.');
            }

            DB::commit();

            return $this->result('Registro removido com sucesso', true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function syncRoutes(Table $table, $routes)
    {
        DB::beginTransaction();

        try {
            $table->routes()->sync($routes);

            DB::commit();

            return $this->result($table, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }
}
