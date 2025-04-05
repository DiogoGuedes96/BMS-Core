<?php

namespace App\Modules\Business\Services;

use App\Modules\Business\Models\Business;
use App\Modules\Business\Models\BusinessPayments;
use App\Modules\Business\Models\BusinessPaymentsResponsible;
use App\Modules\Users\Models\User;
use DateTime;
use Illuminate\Events\NullDispatcher;

class BusinessPaymentService
{
    private $business;
    private $businessPayment;
    private $businessPaymentResponsible;

    public function __construct()
    {
        $this->business = new Business();
        $this->businessPayment = new BusinessPayments();
        $this->businessPaymentResponsible = new BusinessPaymentsResponsible();
    }

    public function generatePayments()
    {
        $businesses = $this->business
            ->where('state_business', 'fechado')
            ->where('closed_State', '!=', 'perdido')
            ->whereNull('canceled_at')
            ->with('referrer')
            ->with('coach')
            ->with('closer')
            ->get();

        [$firstDayOfMonth, $lastDayOfMonth] = $this->getCurrentMonth();

        foreach ($businesses as $business) {
            if ($this->businessPayment
                ->whereBetween('payment_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('business_id', $business->id)
                ->first()
            ) {
                continue;
            }

            $businessPaymentModel = new BusinessPayments();
            $businessPaymentModel->business_id = $business->id;
            $businessPaymentModel->value = $business->value;
            $businessPaymentModel->payment_date = $firstDayOfMonth;
            $businessPaymentModel->save();

            if ($business->referrer) {
                $this->generatePaymentResponsible(
                    'referrer',
                    $business->referrer_commission,
                    $business->referrer_commission_method,
                    $business->referrer,
                    $business,
                    $businessPaymentModel
                );
            }

            if ($business->coach) {
                $this->generatePaymentResponsible(
                    'coach',
                    $business->coach_commission,
                    $business->coach_commission_method,
                    $business->coach,
                    $business,
                    $businessPaymentModel
                );
            }

            if ($business->closer) {
                $this->generatePaymentResponsible(
                    'closer',
                    $business->closer_commission,
                    $business->closer_commission_method,
                    $business->closer,
                    $business,
                    $businessPaymentModel
                );
            }
        }
    }

    public function getCurrentMonth()
    {
        $firstDayOfMonth = new DateTime('first day of -1 month');
        $firstDayOfMonth->setTime(0, 0, 0);


        $lastDayOfMonth = new DateTime('last day of -1 month');
        $lastDayOfMonth->setTime(23, 59, 59);

        return [$firstDayOfMonth, $lastDayOfMonth];
    }

    private function generatePaymentResponsible(
        String $typeResponsible,
        String $comissionResponsible,
        String $methodResponsible,
        User $responsible,
        Business $business,
        BusinessPayments $payment
    ) {
        [$firstDayOfMonth, $lastDayOfMonth] = $this->getCurrentMonth();

        if ($this->businessPaymentResponsible
            ->whereBetween('payment_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('responsible', $typeResponsible)
            ->where('user_id', $responsible->id)
            ->where('business_id', $business->id)
            ->first()
        ) {
            return;
        }

        $lastPayment = $this->businessPaymentResponsible
            ->where('responsible', $typeResponsible)
            ->where('user_id', $responsible->id)
            ->where('business_id', $business->id)
            ->orderBy('id', 'DESC')
            ->first();

        if (
            'encerrar pagamento' != $methodResponsible &&
            $lastPayment &&
            $this->finishPayment($lastPayment, $methodResponsible)
        ) return;

        $model = new BusinessPaymentsResponsible();
        $model->user_id = $responsible->id;
        $model->business_id = $business->id;
        $model->business_payment_id = $payment->id;
        $model->payment_type = $methodResponsible;
        $model->responsible = $typeResponsible;
        $model->value = $comissionResponsible;
        $model->payment_date = $firstDayOfMonth;
        $model->sequence = $lastPayment ? $lastPayment->sequence + 1 : 1;
        $model->save();
    }

    private function finishPayment(BusinessPaymentsResponsible $payment, String $methodResponsible)
    {
        $sequece = $payment->sequence;

        switch ($methodResponsible) {
            case 'recorrente':
                return false;
            case 'encerrar pagamento':
                return $sequece >= 1;
            case 'pagamento total':
                return $sequece >= 1;
            case '3x':
                return $sequece >= 3;
            case '6x':
                return $sequece >= 6;
            case '12x':
                return $sequece >= 12;
        }
    }

    public function listBusiness(
        Bool $pendents,
        DateTime|null $startDate,
        DateTime|null $endDate,
        Int $perPage = 15,
        String $search = null,
        Int|null $userId
    ) {
        $businessPaymentQuery = $this->businessPayment
            ->whereHas('business', function ($query) use ($search, $userId) {
                if ($search) {
                    $query->where('name', 'like', "%$search%");
                }
                if ($userId) {
                    $query->where(function ($q) use ($userId) {
                        $q->orWhere('referrer_id', $userId)
                            ->orWhere('coach_id', $userId)
                            ->orWhere('closer_id', $userId);
                    });
                }
            });

        if ($startDate && $endDate) {
            $businessPaymentQuery->whereBetween('payment_date', [$startDate, $endDate]);
        }

        if (!$pendents) {
            $businessPaymentQuery->whereNull('paid_at');
        } else {
            $businessPaymentQuery->whereNotNull('paid_at');
        }

        $businessPaymentQuery->orderBy('business_id');
        $businessPaymentQuery->orderBy('payment_date');

        $businesses = $businessPaymentQuery->paginate($perPage);

        return $businesses;
    }

    public function listBusinessResposible(BusinessPayments $businessPayment, Int|null $userId)
    {
        $businessQuery = $this->businessPaymentResponsible
            ->where('business_payment_id', $businessPayment->id)
            ->with('business')
            ->with('businessPayment')
            ->with('user')
            ->orderBy('user_id')
            ->orderBy('sequence');

        if ($userId) {
            $businessQuery->where('user_id', $userId);
        }

        $businesses = $businessQuery->get();

        return $businesses;
    }

    public function madePayment(BusinessPayments $businessPayment)
    {
        $paidAt = $businessPayment->paid_at ? null : new DateTime();

        $businessPayment
            ->update(['paid_at' => $paidAt]);

        $this->businessPaymentResponsible
            ->where('business_payment_id', $businessPayment->id)
            ->update(['paid_at' => $paidAt]);

        return $businessPayment;
    }
}
