<?php

namespace App\Modules\Bookings\Services;

use App\Services\Service;
use Illuminate\Support\Facades\DB;
use App\Modules\Bookings\Models\BookingClient;

class ClientService extends Service
{
    const SEARCHABLE_FIELDS = [
        'name',
        'email',
        'phone'
    ];

    public function getAll($request)
    {
        $order = $request->order ?? 'name-asc';

        [$field, $direction] = explode('-', $order);

        $clients = BookingClient::orderBy($field, $direction);

        if ($request->filled('search')) {
            $search = $request->search;

            $clients->where(function($query) use ($search) {
                foreach(self::SEARCHABLE_FIELDS as $key => $field) {
                    if ($key > 0) {
                        $query->orWhere($field, 'like', '%'. $search .'%');
                    } else {
                        $query->where($field, 'like', '%'. $search .'%');
                    }
                }
            });
        }

        $clients = ($request->has('page'))
			? $clients->paginate((int) $request->per_page ?? 10)
			: $clients->get();

        return $clients;
    }

    public function getById($id)
    {
        return BookingClient::find($id);
    }

    public function getByEmail($email)
    {
        return BookingClient::where('email', '=', $email)->first();
    }

    public function create($request)
    {
        DB::beginTransaction();

        try {
            if (!isset($request['active'])) {
                $request['active'] = true;
            }

            if (!$client = BookingClient::create($request)) {
                throw new \Exception('Houve um erro ao tentar guardar o registro.');
            }

            DB::commit();

            return $this->result($client, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function update(BookingClient $client, $request)
    {
        DB::beginTransaction();

        try {
            if (!$client->update($request)) {
                throw new \Exception('Houve um erro ao tentar atualizar o registro.');
            }

            DB::commit();

            return $this->result($client, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function delete(BookingClient $client)
    {
        DB::beginTransaction();

        try {
            if (!$client->delete()) {
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
