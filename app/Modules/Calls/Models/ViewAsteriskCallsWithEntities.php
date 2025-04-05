<?php

namespace App\Modules\Calls\Models;

use Illuminate\Database\Eloquent\Model;

class ViewAsteriskCallsWithEntities extends Model
{
    protected $table = 'calls_with_entities_view';

    protected $fillable = [
        'call_id',
        'caller_phone',
        'linkedid',
        'call_status',
        'call_client_name',
        'call_created_at',
        'call_hangup_status',
        'call_reason',
        'call_operator',
        'callee_phone',

        'client_id',
        'client_name',
        'client_email',
        'client_type',
        'client_address',
        'client_nif',
        'client_phone',
        'client_status',

        'client_responsible_id',
        'client_responsible_name',
        'client_responsible_phone',

        'patient_id',
        'patient_name',
        'patient_number',
        'patient_nif',
        'patient_birthday',
        'patient_email',
        'patient_address',
        'patient_postal_code',
        'patient_postal_code_address',
        'patient_transport_feature',
        'patient_observations',
        'patient_status',

        'patient_phone',
        'patient_responsible_id',
        'patient_responsible_name',
        'patient_responsible_phone',

        'operator_id',
        'operator_name',
        'operator_email',
    ];

    public $timestamps = false;
    protected $dates = ['call_created_at'];
}