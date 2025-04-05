<?php

namespace App\Modules\Calls\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhoneBlackList extends Model
{
    use HasFactory;

    protected $table = 'phone_blacklist';
    protected $primaryKey = 'id';

    protected $fillable = [
        'phone',
    ];
}
