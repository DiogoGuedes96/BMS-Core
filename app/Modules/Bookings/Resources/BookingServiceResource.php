<?php

namespace App\Modules\Bookings\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Modules\Workers\Resources\WorkerResource;
use App\Modules\Services\Resources\ServiceResource;
use App\Modules\Services\Resources\ServiceStateResource;

class BookingServiceResource extends JsonResource
{
    public function toArray($request)
    {
        $relationships = [
            'booking' => new BookingResource($this->booking),
            'serviceType' => new ServiceResource($this->serviceType),
            'serviceState' => new ServiceStateResource($this->serviceState),
            'staff' => new WorkerResource($this->staff),
            'supplier' => new WorkerResource($this->supplier),
        ];

        if ($request->has('withChild')) {
            $relationships['child'] = new BookingServiceResource($this->child);
        }

        if ($request->has('withHistoric')) {
            $relationships['historic'] = new BookingServiceHistoricCollection(
                $this->audits()->with('user')->orderBy('created_at', 'DESC')
                    ->whereIn('event', ['created', 'updated'])->get()
            );
        }

        return [
            'data' => [
                'id' => $this->id,
                'start' => $this->start,
                'hour' => $this->hour,
                'number_adults' => $this->number_adults,
                'number_children' => $this->number_children,
                'number_baby_chair' => $this->number_baby_chair,
                'booster' => $this->booster,
                'flight_number' => $this->flight_number,
                'vehicle_text' => $this->vehicle_text,
                'car_type' => $this->car_type,
                'pickup_location' => $this->pickup_location,
                'pickup_zone' => $this->pickup_zone,
                'pickup_address' => $this->pickup_address,
                'pickup_reference_point' => $this->pickup_reference_point,
                'dropoff_location' => $this->dropoff_location,
                'dropoff_zone' => $this->dropoff_zone,
                'dropoff_address' => $this->dropoff_address,
                'dropoff_reference_point' => $this->dropoff_reference_point,
                'value' => $this->value,
                'charge' => $this->charge,
                'commission' => $this->commission,
                'notes' => $this->notes,
                'internal_notes' => $this->internal_notes,
                'driver_notes' => $this->driver_notes,
                'emphasis' => $this->emphasis,
                'voucher' => $this->voucher ?? [
                    'start' => $this->start,
                    'hour' => $this->hour,
                    'pickup_location' => $this->pickup_location,
                    'dropoff_location' => $this->dropoff_location
                ],
                'booking_id' => $this->booking_id,
                'service_type_id' => $this->service_type_id,
                'service_state_id' => $this->service_state_id,
                'staff_id' => $this->staff_id,
                'supplier_id' => $this->supplier_id,
                'vehicle_id' => $this->vehicle_id,
                'pickup_location_id' => $this->pickup_location_id,
                'pickup_zone_id' => $this->pickup_zone_id,
                'dropoff_location_id' => $this->dropoff_location_id,
                'dropoff_zone_id' => $this->dropoff_zone_id,
                'parent_id' => $this->parent_id,
                'was_paid' => $this->was_paid,
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
            ],
            'relationships' => $relationships
        ];
    }
}
