<?php

namespace App\Modules\Workers\Services;

use Illuminate\Support\Facades\DB;
use App\Modules\Workers\Models\Worker;
use App\Modules\Users\Models\User;
use App\Modules\Users\Services\UserProfileService;
use App\Modules\Workers\Enums\StatusEnum;
use App\Modules\Workers\Helpers\TransformFieldsHelper;
use App\Services\Service;

class WorkerService extends Service
{
    const SEARCHABLE_FIELDS = [
        'id',
        'name',
        'phone',
        'social_denomination',
        'nif',
        'responsible_name',
        'address',
        'postal_code',
        'locality',
        'antecedence',
        'username',
        'email',
        'notes',
    ];

    public function __construct(
        private UserProfileService $userProfileService
    )
    {
    }

    public function getAll($request)
    {
        $tableName = app(Worker::class)->getTable() . '.';

        $order = $request->order ?? 'name-asc';

        [$field, $direction] = explode('-', $order);

        $workers = Worker::select($tableName . '*')
            ->join('bms_tables', $tableName . 'table_id', '=', 'bms_tables.id')
            ->where($tableName . 'type', '=', $request->type);

        if ($field == 'table') $field = 'bms_tables.name';
        else $field = $tableName . $field;

        $workers->orderBy($field, $direction);

        if ($request->filled('search')) {
            $search = $request->search;

            $workers->where(function($query) use ($search, $tableName) {
                foreach(self::SEARCHABLE_FIELDS as $key => $field) {
                    if ($key > 0) {
                        $query->orWhere($tableName . $field, 'like', '%'. $search .'%');
                    } else {
                        $query->where($tableName . $field, 'like', '%'. $search .'%');
                    }
                }
            });
        }

        if ($request->filled('worker_id')) {
            if (strpos($request->worker_id, ',') !== FALSE) {
                $workers->whereIn($tableName . 'id', explode(',', $request->worker_id));
            } else {
                $workers->where($tableName . 'id', '=', $request->worker_id);
            }
        }

        if ($request->filled('table_id')) {
            $workers->where($tableName . 'table_id', '=', $request->table_id);
        }

        if ($request->filled('vehicle_id')) {
            $workers->where($tableName . 'vehicle_id', '=', $request->vehicle_id);
        }

        if ($request->filled('status')) {
            $workers->where($tableName . 'active', '=', StatusEnum::getAll()[$request->status]);
        }

        $workers = ($request->has('page'))
			? $workers->paginate((int) $request->per_page ?? 10)
			: $workers->get();

        return $workers;
    }

    public function getById($id)
    {
        return Worker::find($id);
    }

    public function getByUsername($username)
    {
        return Worker::where('username', '=', $username)->where('active', true)->first();
    }

    public function getByEmail($email)
    {
        return Worker::where('active', true)
            ->whereHas('user', function($query) use ($email) {
                $query->where('email', '=', $email);
            })
            ->first();
    }

    public function create($request)
    {
        DB::beginTransaction();

        try {
            if (!isset($request['active'])) {
                $request['active'] = true;
            }

            if (!empty($request['postal_code'])) {
                $request['postal_code'] = TransformFieldsHelper::postalCode($request['postal_code']);
            }

            if (in_array($request['type'], ['operators', 'staff'])) {
                $user = $this->createOrUpdateUser([
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'phone' => !empty($request['phone']) ? str_replace(' ', '', trim($request['phone'] ?? '')) : null,
                    'profile_id' => $this->userProfile($request['type'])
                ]);

                if (!$user) {
                    throw new \Exception('Houve um erro ao tentar guardar o registro.');
                }

                $request['user_id'] = $user->id;
            }

            if (!$worker = Worker::create($request)) {
                throw new \Exception('Houve um erro ao tentar guardar o registro.');
            }

            DB::commit();

            return $this->result($worker, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function update(Worker $worker, $request)
    {
        DB::beginTransaction();

        try {
            if (!empty($request['postal_code'])) {
                $request['postal_code'] = TransformFieldsHelper::postalCode($request['postal_code']);
            }

            if (!$worker->update($request)) {
                throw new \Exception('Houve um erro ao tentar atualizar o registro.');
            }

            if (in_array($worker->type, ['operators', 'staff'])) {
                $user = $this->createOrUpdateUser([
                    'name' => $request['name'] ?? $worker->name,
                    'email' => $request['email'] ?? $worker->email,
                    'phone' => !empty($request['phone']) ? str_replace(' ', '', trim($request['phone'] ?? '')) : null,
                    'profile_id' => $this->userProfile($worker->type)
                ], $worker->user_id);

                if (!$user) {
                    throw new \Exception('Houve um erro ao tentar atualizar o registro.');
                }

                $worker->user_id = $user->id;
                $worker->save();
            }

            DB::commit();

            return $this->result($worker, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    private function userProfile($type)
    {
        $roles = ['operators' => 'operator', 'staff' => 'staff'];

        $userProfile = $this->userProfileService->getByRole($roles[$type]);

        return $userProfile->id;
    }

    private function createOrUpdateUser($data, $user_id = null)
    {
        if (!empty($user_id) && $user = User::find($user_id)) {
            $user->update($data);
            return $user;
        }

        if (!$user = User::where('email', '=', $data['email'])->first()) {
            return User::create($data);
        }

        $user->update($data);
        return $user;
    }

    public function delete(Worker $worker)
    {
        DB::beginTransaction();

        try {
            if (!$worker->delete()) {
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
