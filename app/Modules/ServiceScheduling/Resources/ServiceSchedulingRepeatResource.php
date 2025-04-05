<?php

namespace App\Modules\ServiceScheduling\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceSchedulingRepeatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'is_repeat_schedule' => $this->is_repeat_schedule,
            'repeat_date' => $this->repeat_date,
            'repeat_time' => $this->repeat_time,
            'repeat_days' => $this->repeat_days,
            'repeat_finish_by' => $this->repeat_finish_by,
            'repeat_final_date' => $this->repeat_final_date,
            'repeat_number_sessions' => $this->repeat_number_sessions
        ];
    }
}
