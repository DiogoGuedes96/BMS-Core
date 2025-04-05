<?php

namespace App\Modules\Business\Commands;

use App\Modules\Business\Services\BusinessPaymentService;
use Illuminate\Console\Command;
use Exception;

class UniGeneratePayments extends Command
{

    /**
     * @var BusinessPaymentService
     */
    protected $businessPayments;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uni:generate-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera os pagamentos para todos os negocios ativos e para os responsaveis do négocio.';

    public function __construct(BusinessPaymentService $businessPaymentService)
    {
        parent::__construct();
        $this->businessPayments = $businessPaymentService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->businessPayments->generatePayments();
    }
}
