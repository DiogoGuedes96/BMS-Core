<?php

namespace App\Modules\Users\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $table = 'user_profile';

    protected $fillable = [
        'role',
        'description',
        'active'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'readonly' => 'boolean'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'profile_id')->where('active', '=', true);
    }

    public function userProfileModules()
    {
        return $this->hasMany(UserProfileModules::class, 'profile_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
