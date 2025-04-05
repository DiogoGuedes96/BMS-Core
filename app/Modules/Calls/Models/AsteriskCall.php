<?php

namespace App\Modules\Calls\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class AsteriskCall extends Model
{
    use HasFactory;

    protected $table = 'asterisk_calls';
    protected $primaryKey = 'id';


    protected $fillable = [
        'caller_phone',
        'linkedid',
        'status',
        'client_name',
        'hangup_status',
        'call_reason',
        'call_operator',
        'callee_phone'
    ];

    public function operator() : BelongsTo {
        return $this->belongsTo(User::class, 'call_operator', 'id');
    }
}
