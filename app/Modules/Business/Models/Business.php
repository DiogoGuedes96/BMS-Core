<?php

namespace App\Modules\Business\Models;

use App\Modules\Products\Models\Products;
use App\Modules\UniClients\Models\UniClients;
use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Business extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $table = 'business';

    protected $fillable = [
        'client_id',
        'name',
        'value',
        'product_id',
        'business_kanban_id',
        'stage',
        'state_business',
        'referrer_id',
        'referrer_commission',
        'referrer_commission_method',
        'coach_id',
        'coach_commission',
        'coach_commission_method',
        'closer_id',
        'closer_commission',
        'closer_commission_method',
        'description',
        'index',
        'closed_state',
        'closed_at',
        'canceled_at',
        'acId'
    ];

    protected $auditInclude = [
        'client_id',
        'name',
        'value',
        'product_id',
        'business_kanban_id',
        'stage',
        'state_business',
        'referrer_id',
        'referrer_commission',
        'referrer_commission_method',
        'coach_id',
        'coach_commission',
        'coach_commission_method',
        'closer_id',
        'closer_commission',
        'closer_commission_method',
        'description',
        'index',
        'closed_state',
        'closed_at',
        'canceled_at'
    ];

    public function client()
    {
        return $this->belongsTo(UniClients::class, 'client_id');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function closer()
    {
        return $this->belongsTo(User::class, 'closer_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function businessKanban()
    {
        return $this->belongsTo(BusinessKanban::class, 'business_kanban_id');
    }

    public function stage()
    {
        return $this->belongsTo(BusinessKanbanColumns::class, 'stage');
    }

    public function followUp()
    {
        return $this->hasMany(BusinessFollowup::class, 'business_id', 'id');
    }
}
