<?php

namespace App\Modules\Bookings\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Modules\Companies\Services\CompanyService;

class BookingResource extends JsonResource
{
    public function toArray($request)
    {
        $imgBase64 = '';

        if (empty($this->voucher)) {
            $company = app(CompanyService::class)->getCompany();
            $img = $company->img_url ? asset(Storage::url($company->img_url)) : '';

            if ($request->has('onlyOnGetById')) {
                $imgDir = storage_path('app/public/' . $company->img_url);
                $type = pathinfo($imgDir, PATHINFO_EXTENSION);
                $data = file($imgDir);
                $imgBase64 = 'data:image/' . $type . ';base64,' . base64_encode(implode('', $data));
            }
        }

        return [
            'data' => [
                'id' => $this->id,
                'client_name' => $this->client_name,
                'client_email' => $this->client_email,
                'client_phone' => $this->client_phone,
                'value' => $this->value,
                'deposits_paid' => $this->deposits_paid,
                'pax_group' => $this->pax_group,
                'created_by' => $this->created_by,
                'start_date' => $this->start_date,
                'hour' => $this->hour,
                'additional_information' => $this->additional_information,
                'emphasis' => $this->emphasis,
                'status' => $this->status,
                'voucher' => $this->voucher ?? [
                    'company_name' => $company->name ?? '',
                    'company_phone' => $company->phone ?? '',
                    'company_email' => $company->email ?? '',
                    'company_img' => $img,
                    'company_imgbase64' => $imgBase64,
                    'client_name' => $this->client_name,
                    'pax_group' => $this->pax_group,
                    'operator' => !empty($this->operator) ? $this->operator->name : ''
                ],
                'was_paid' => $this->was_paid,
                'reference' => $this->reference,
                'operator_id' => $this->operator_id,
                'booking_client_id' => $this->booking_client_id,
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
            ],
            'relationships' => [
                'operator' => new OperatorResource($this->operator),
                'client' => new ClientResource($this->client),
                'serviceVouchers' => $this->services->map(function($service) {
                    return [
                        'id' => $service->id,
                        'voucher' => $service->voucher ?? [
                            'start' => $service->start,
                            'hour' => $service->hour,
                            'pickup_location' => $service->pickup_location,
                            'dropoff_location' => $service->dropoff_location
                        ]
                    ];
                })
            ]
        ];
    }
}
