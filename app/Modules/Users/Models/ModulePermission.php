<?php

namespace App\Modules\Users\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModulePermission extends Model
{
    use HasFactory;

    protected $table = 'module_permissions';

    protected $fillable = [
        'module',
        'label',
        'permissions',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'permissions' => 'json'
    ];
}
