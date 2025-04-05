<?php

namespace App\Modules\Users\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfileModules extends Model
{
    use HasFactory;

    protected $table = 'user_profile_modules';

    protected $fillable = [
        'profile_id',
        'module',
        'permissions'
    ];

    protected $casts = [
        'permissions' => 'json'
    ];

    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class, 'profile_id');
    }
}
