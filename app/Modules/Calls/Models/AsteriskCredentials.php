<?php

namespace App\Modules\Calls\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AsteriskCredentials extends Model
{
    use HasFactory;

    protected $table = 'asterisk_credentials';
    protected $primaryKey = 'id';

    protected $fillable = [
        'host',
        'scheme',
        'port',
        'username',
        'secret',
        'connect_timeout',
        'read_timeout',
        'internal_pw',
    ];
}
