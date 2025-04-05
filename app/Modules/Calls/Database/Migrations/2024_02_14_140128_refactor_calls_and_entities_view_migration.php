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
                
                COALESCE(pt.id, ct.id, ctr.id, ptr.id) as entity_id,
                
                COALESCE(
                    CASE WHEN pt.id IS NOT NULL THEN pt.name END,
                    CASE WHEN ct.id IS NOT NULL THEN ct.name END,
                    CASE WHEN ctr.id IS NOT NULL THEN ctr.name END,
                    CASE WHEN ptr.id IS NOT NULL THEN ptr.patient_responsible END
                ) as entity_name,
            
                COALESCE(
                    CASE WHEN pt.id IS NOT NULL THEN pt.nif END,
                    CASE WHEN ct.id IS NOT NULL THEN ct.nif END
                ) as entity_nif,
            
                COALESCE(
                    CASE WHEN pt.id IS NOT NULL THEN pt.email END,
                    CASE WHEN ct.id IS NOT NULL THEN ct.email END
                ) as entity_email,
            
                COALESCE(
                    CASE WHEN pt.id IS NOT NULL THEN pt.address END,
                    CASE WHEN ct.id IS NOT NULL THEN ct.address END
                ) as entity_address,
            
                COALESCE(
                    CASE WHEN pt.id IS NOT NULL THEN pt.phone_number END,
                    CASE WHEN ct.id IS NOT NULL THEN ct.phone END,
                    CASE WHEN ptr.id IS NOT NULL THEN ptr.phone_number END,
                    CASE WHEN ctr.id IS NOT NULL THEN ctr.phone END
                ) as entity_phone,
                
                CASE
                    WHEN pt.id IS NOT NULL THEN 'patient'
                    WHEN ct.id IS NOT NULL THEN 'client'
                    WHEN ctr.id IS NOT NULL THEN 'client responsible'
                    WHEN ptr.id IS NOT NULL THEN 'patient responsible'
                    ELSE NULL
                END as entity_type,
                
                u.id as operator_id,
                u.name as operator_name,
                u.email as operator_email
            
            FROM `asterisk_calls` as ac
            LEFT JOIN patients as pt ON ac.callee_phone = pt.phone_number OR ac.caller_phone = pt.phone_number
            LEFT JOIN clients as ct ON ac.callee_phone = ct.phone OR ac.caller_phone = ct.phone
            LEFT JOIN client_responsible as ctr ON ac.callee_phone = ctr.phone OR ac.caller_phone = ctr.phone
            LEFT JOIN patient_responsible as ptr ON ac.callee_phone = ptr.phone_number OR ac.caller_phone = ptr.phone_number
            LEFT JOIN users as u ON ac.call_operator = u.id;
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
