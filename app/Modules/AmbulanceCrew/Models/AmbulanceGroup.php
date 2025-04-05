<?php

namespace App\Modules\AmbulanceCrew\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AmbulanceGroup extends Model
{
    
    use HasFactory, SoftDeletes;

    protected $table = "ambulance_groups";
    protected $primaryKey = "id";

    protected $fillable = [
        'name'
    ];

    public function crew(): HasMany {
        return $this->hasMany(AmbulanceCrew::class, 'group_id', 'id');
    }
}
