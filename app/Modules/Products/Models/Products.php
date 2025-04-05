<?php

namespace App\Modules\Products\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'bms_products';
    protected $primaryKey = 'id';

    protected $fillable = [
        "name",
        "value",
        "commission",
        "coin",
        "status",
    ];
}
