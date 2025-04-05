<?php

namespace App\Modules\Business\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessPaymentResponsibleResource extends JsonResource
{
    public function toArray($request)
    {
        Carbon::setLocale('pt_BR');

        $date = Carbon::createFromFormat('Y-m-d H:i:s', $this->payment_date);

        if (is_string($this->paid_at)) {
            $datePaid = $this->paid_at ? Carbon::createFromFormat('Y-m-d H:i:s', $this->paid_at) : null;
        } else {
            $datePaid = $this->paid_at ? Carbon::instance($this->paid_at) : null;
        }

        $monthName = $date->translatedFormat('F');

        return [
            "id" => $this->id,
            "user" => $this->user,
            // "business_id" => $this->business_id,
            // "business_payment_id" => $this->business_payment_id,
            "payment_type" => $this->payment_type,
            "responsible" => $this->responsible,
            "value" => $this->value,
            "sequence" => $this->sequence,
            "payment_date" => $date->format('m/Y'),
            "month_payment_date" => $monthName,
            "paid_at" => $datePaid ? $datePaid->format('d/m/Y') : $datePaid,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
