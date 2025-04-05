<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE VIEW IF NOT EXISTS calls_with_entities_view AS
            SELECT
                ac.id as call_id,
                ac.caller_phone,
                ac.linkedid,
                ac.status as call_status,
                ac.client_name as call_client_name,
                ac.created_at as call_created_at,
                ac.hangup_status as call_hangup_status,
                ac.call_reason,
                ac.call_operator,
                ac.callee_phone,

                ct.id as client_id,
                ct.name as client_name,
                ct.email as client_email,
                ct.type as client_type,
                ct.address as client_address,
                ct.nif as client_nif,
                ct.phone as client_phone,
                ct.status as client_status,

                ctr.id as client_responsible_id,
                ctr.name as client_responsible_name,
                ctr.phone as client_responsible_phone,

                pt.id as patient_id,
                pt.name as patient_name,
                pt.patient_number as patient_number,
                pt.nif as patient_nif,
                pt.birthday as patient_birthday,
                pt.email as patient_email,
                pt.address as patient_address,
                pt.postal_code as patient_postal_code,
                pt.postal_code_address as patient_postal_code_address,
                pt.transport_feature as patient_transport_feature,
                pt.patient_observations as patient_observations,
                pt.status as patient_status,
                pt.phone_number as patient_phone,

                ptr.id as patient_responsible_id,
                ptr.patient_responsible as patient_responsible_name,
                ptr.phone_number as patient_responsible_phone,

                u.id as operator_id,
                u.name as operator_name,
                u.email as operator_email

            FROM `asterisk_calls` as ac
            LEFT JOIN clients as ct
                ON ac.callee_phone = ct.phone OR ac.caller_phone = ct.phone
            LEFT JOIN client_responsible as ctr
                ON ac.callee_phone = ctr.phone OR ac.caller_phone = ctr.phone
            LEFT JOIN patients as pt
                ON ac.callee_phone = pt.phone_number OR ac.caller_phone = pt.phone_number
            LEFT JOIN patient_responsible as ptr
                ON ac.callee_phone = ptr.phone_number OR ac.caller_phone = ptr.phone_number
            LEFT JOIN users as u
                ON ac.call_operator = u.id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS calls_with_entities_view');
    }
};
