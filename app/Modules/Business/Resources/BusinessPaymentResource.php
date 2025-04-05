<?php

namespace App\Modules\Business\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Date;

// Set the locale to Portuguese


class BusinessPaymentResource extends JsonResource
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
            "business" => $this->business,
            "value" => $this->value,
            "payment_date" => $date->format('m/Y'),
            "month_payment_date" => $monthName,
            "paid_at" => $datePaid ? $datePaid->format('d/m/Y') : $datePaid,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
