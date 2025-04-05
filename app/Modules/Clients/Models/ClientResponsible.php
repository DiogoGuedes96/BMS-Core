<?php

namespace App\Modules\Clients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ClientResponsible extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'client_responsible';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'phone',
    ];

    public function clients()
    {
        return $this->belongsToMany(Clients::class, 'clients_have_responsibles', 'client_responsible_id', 'client_id')->withTimestamps();;
    }
}
