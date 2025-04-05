<?php

namespace App\Modules\Clients\Models;

use App\Modules\Patients\Models\Patients;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Clients extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'clients';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'email',
        'type',
        'address',
        'nif',
        'phone',
        'status',
    ];

    public function clientResponsibles()
    {
        return $this->belongsToMany(ClientResponsible::class, 'clients_have_responsibles', 'client_id', 'client_responsible_id')->withTimestamps();
    }

    public function patients()
    {
        return $this->belongsToMany(Patients::class, 'clients_have_patients', 'client_id', 'patient_id')
            ->withTimestamps();
    }

    /*public function getTypeAttribute($value)
    {
        if ($value === 'public') {
            return 'Público';
        } elseif ($value === 'private') {
            return 'Privado';
        }

        return $value;
    }*/
}
