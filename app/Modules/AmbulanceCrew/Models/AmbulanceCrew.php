<?php

namespace App\Modules\AmbulanceCrew\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AmbulanceCrew extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "ambulance_crew";
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'email',
        'address',
        'status',
        'nif',
        'driver_license',
        'contract_number',
        'contract_date',
        'phone_number',
        'job_title'
    ];

    public function group(): BelongsTo {
        return $this->belongsTo(AmbulanceGroup::class);
    }
}
