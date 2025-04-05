<?php

namespace App\Modules\Business\Resources;

use App\Modules\Business\Models\BusinessPaymentsResponsible;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
{
    public function toArray($request)
    {
        $referrerPayment = BusinessPaymentsResponsible::where('user_id', $this->referrer_id)->where('business_id', $this->id)->first();
        $coachPayment = BusinessPaymentsResponsible::where('user_id', $this->coach_id)->where('business_id', $this->id)->first();
        $closerPayment = BusinessPaymentsResponsible::where('user_id', $this->closer_id)->where('business_id', $this->id)->first();

        $return = [
            'data' => [
                'id' => $this->id,
                'client_id' => $this->client_id,
                'name' => $this->name,
                'value' => $this->value,
                'product' => $this->product,
                'product_id' => $this->product_id,
                'business_kanban_id' => $this->business_kanban_id,
                'stage' => $this->stage,
                'state_business' => $this->state_business,
                'closed_state' => $this->closed_state,
                'referrer_id' => $this->referrer_id,
                'referrer_commission' => $this->referrer_commission,
                'referrer_commission_method' => $this->referrer_commission_method,
                'referrer_payment_status' => !empty($referrerPayment->status) ? $referrerPayment->status : null,
                'referrer' => $this->referrer,
                'coach_id' => $this->coach_id,
                'coach_commission' => $this->coach_commission,
                'coach_commission_method' => $this->coach_commission_method,
                'coach_payment_status' => !empty($coachPayment->status) ? $coachPayment->status : null,
                'coach' => $this->coach,
                'closer_id' => $this->closer_id,
                'closer_commission' => $this->closer_commission,
                'closer_commission_method' => $this->closer_commission_method,
                'closer_payment_status' => !empty($closerPayment->status) ? $closerPayment->status : null,
                'closer' => $this->closer,
                'description' => $this->description,
                'client' => $this->client,
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
                'canceled_at' => $this->canceled_at,
                'historic' => new BusinessHistoricCollection(
                    $this->audits()->with('user')->orderBy('created_at', 'DESC')
                        ->whereIn('event', ['created', 'updated'])->get()
                )
            ],
        ];

        return $return;
    }
}
