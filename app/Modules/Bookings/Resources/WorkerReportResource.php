<?php

namespace App\Modules\Bookings\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Bookings\Services\ReportService;

class WorkerReportResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'social_denomination' => $this->social_denomination,
            'nif' => $this->nif,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'type' => $this->type,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];

        $request['workerId'] = $this->id;
        $request['workerType'] = $this->type;

        if ($this->type != 'operators') {
            $services = app(ReportService::class)
            ->getResumeServicesValueFromTable(
                $request,
                $this->type != 'staff' ? substr($this->type, 0, -1) : 'staff'
            );
        } else {
            $services = app(ReportService::class)->getResumeServicesByWorker($request);
            $bookings = app(ReportService::class)->getResumeBookingsByOperator($request);
        }

        if ($request->has('byMonth')) {
            if ($this->type == 'operators') {
                $byMonth = $this->groupByMonth($bookings);
            }

            if ($this->type == 'suppliers') {
                $byMonth = $this->groupByMonth($services);
            }

            $data['byMonth'] = $byMonth ?? [];
        } else {
            $data['totalBookings'] = !empty($bookings) ? $bookings->count() : 0;
            $data['totalValueBookings'] = !empty($bookings) ? $bookings->sum('value') : 0;
            $data['totalServices'] = $services->count();
            $data['totalValueServices'] = $services->sum('value');
        }

        return compact('data');
    }

    private function groupByMonth($data)
    {
        $dataModel = [
            'value' => 0,
            'quantity' => 0
        ];

        $dataByMonth = [
            'Janeiro' => (object) ['month' => 'Janeiro', ...$dataModel],
            'Fevereiro' => (object) ['month' => 'Fevereiro', ...$dataModel],
            'Março' => (object) ['month' => 'Março', ...$dataModel],
            'Abril' => (object) ['month' => 'Abril', ...$dataModel],
            'Maio' => (object) ['month' => 'Maio', ...$dataModel],
            'Junho' => (object) ['month' => 'Junho', ...$dataModel],
            'Julho' => (object) ['month' => 'Julho', ...$dataModel],
            'Agosto' => (object) ['month' => 'Agosto', ...$dataModel],
            'Setembro' => (object) ['month' => 'Setembro', ...$dataModel],
            'Outubro' => (object) ['month' => 'Outubro', ...$dataModel],
            'Novembro' => (object) ['month' => 'Novembro', ...$dataModel],
            'Dezembro' => (object) ['month' => 'Dezembro', ...$dataModel],
        ];

        $data->each(function (object $item) use ($dataByMonth) {
            $dataByMonth[$item->month]->value += $item->value;
            $dataByMonth[$item->month]->quantity += 1;
        });

        return array_values($dataByMonth);
    }
}
