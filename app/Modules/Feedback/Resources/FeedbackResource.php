<?php

namespace App\Modules\Feedback\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
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
            'name' => $this->name,
            'patientNumber' => $this->patient_number,
            'reason' => $this->reason,
            'date' => $this->date,
            'time' => $this->time,
            'description' => $this->description,

            $this->mergeWhen($this->feedbackWho, [
                'feedbackWho' => FeedbackWhoResource::collection($this->feedbackWho)
            ])
        ];
    }
}
