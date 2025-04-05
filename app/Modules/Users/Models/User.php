<?php

namespace App\Modules\Users\Models;

use App\Modules\Business\Models\Business;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Laravel\Sanctum\HasApiTokens;
use App\Modules\Workers\Models\Worker;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements CanResetPassword
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, \Illuminate\Auth\Passwords\CanResetPassword;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'token',
        'refresh_token',
        'phone',
        'profile_id',
        'active',
        'first_access',
        'last_access',
        'settings'
    ];
    
    protected $casts = [
        'active' => 'boolean',
        'settings' => 'json'
    ];

    protected $dates = [
        'first_access',
        'last_access',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $rememberTokenName = false;

    public function profile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'profile_id', 'id');
    }

    public function referrer(): HasMany
    {
        return $this->hasMany(Business::class, 'referrer_id', 'id')
            ->whereMonth('created_at', DB::raw('MONTH(CURRENT_DATE())'));
    }

    public function coach(): HasMany
    {
        return $this->hasMany(Business::class, 'coach_id', 'id')
            ->whereMonth('created_at', DB::raw('MONTH(CURRENT_DATE())'));
    }

    public function closer(): HasMany
    {
        return $this->hasMany(Business::class, 'closer_id', 'id')
            ->whereMonth('created_at', DB::raw('MONTH(CURRENT_DATE())'));
    }

    public function calls(): HasMany
    {
        return $this->hasMany(AsteriskCall::class, 'call_operator', 'id');
    }

    public function worker()
    {
        return $this->hasOne(Worker::class);
    }
}
