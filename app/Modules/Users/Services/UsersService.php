<?php

namespace App\Modules\Users\Services;

use App\Modules\Users\Models\User;
use App\Modules\Users\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\Service;
use App\Modules\Users\Enums\StatusEnum;

class UsersService extends Service
{
    public function list($request, $role = null)
    {
        if ($request->filled('sorter')) {
            $field = $request->fieldSorter ?? 'name';
            $direction = substr($request->sorter, 0, -3);
        } else {
            $order = $request->order ?? 'name-asc';
            [$field, $direction] = explode('-', $order);
        }

        $users = User::with(['profile'])->orderBy($field, $direction);

        if ($request->filled('role') || !empty($role)) {
            $role = $request->role ?? $role;

            $users->whereHas('profile', function ($query) use ($role) {
                $query->where('role', '=', $role);
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $users->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $users->where('active', '=', StatusEnum::getAll()[$request->status]);
        }

        $users = ($request->has('page'))
            ? $users->paginate((int) $request->per_page ?? 10)
            : $users->get();

        return $users;
    }

    public function getById($id)
    {
        return User::find($id);
    }

    public function getTotal(): int
    {
        return User::count();
    }

    public function createUser(Request $request): User
    {
        $active = !$request->has('active') ? true : $request->active;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone ?? null,
            'profile_id' => $request->profile_id ?? $request->profile,
            'active' => $active
        ]);

        return $user;
    }

    public function updateUser(Request $request, $id): User
    {
        $user = User::findOrFail($id);

        $userData = [
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
            'phone' => $request->phone ?? $user->phone,
            'profile_id' => $request->profile_id ?? $request->profile ?? $user->profile_id,
            'active' => $request->active ?? $user->active,
            'settings' => $request->settings ?? $user->settings,
        ];

        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        $user->update($userData);

        return $user;
    }

    public function updateLoginDate($user): User
    {
        $now = date('Y-m-d H:i:s');
        $user->last_access = $now;

        if (empty($user->first_access)) {
            $user->first_access = $now;
        }

        $user->save();

        return $user;
    }

    public function inactivate($id): User
    {
        $user = User::findOrFail($id);
        $user->active = false;
        $user->save();

        return $user;
    }

    public function delete(User $user)
    {
        DB::beginTransaction();

        try {
            if (!$user->delete()) {
                throw new \Exception('Houve um erro ao tentar excluir o registro.');
            }

            DB::commit();

            return $this->result('Registro removido com sucesso', true);
        } catch (\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function changePassword(Request $request, $user): User
    {
        if (Hash::check($request->input('current_password'), $user->password)) {
            $user->update([
                'password' => bcrypt($request->input('new_password')),
            ]);

            return $user;
        }

        return false;
    }

    public function forceChangePassword(Request $request, $user): User
    {
        $user->update([
            'password' => bcrypt($request->input('new_password')),
        ]);

        return $user;
    }

    public function logout($user): void
    {
        $user->tokens()->delete();
    }

    public function hardDelete(int $id)
    {
        try {
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Delete the client using a raw SQL query
            DB::table('users')->where('id', $id)->delete();

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Throwable $e) {
            throw new \Exception('Houve um erro ao tentar excluir permanentemente o registro.');
        }
    }
}
