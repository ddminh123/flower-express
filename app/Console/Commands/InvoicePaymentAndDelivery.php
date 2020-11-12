<?php

namespace App\Console\Commands;

use App\Models\KiotVietInvoice;
use App\Models\KiotVietInvoiceDelivery;
use App\Models\KiotVietInvoicePayment;
use App\KiotVietService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class InvoicePaymentAndDelivery extends Command
{
    protected $service;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:payment-and-delivery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->service = new KiotVietService();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        KiotVietInvoice::query()->select('_id', 'id', 'code', 'payments', 'invoiceDelivery', 'status_send')
            ->whereNull('invoiceDelivery')->orWhere('invoiceDelivery', '')->orderByDesc('_id')
            ->chunk(1000, function ($invoices) {
                foreach ($invoices as $invoice) {
                    try {
                        $this->info($invoice->code);

                        $invoiceApi = $this->service->findInvoiceByCode($invoice->code);

                        if ($invoiceApi && !empty($invoiceApi['invoiceDelivery'])) {
                            $expectedDelivery = $invoiceApi['purchaseDate'];
                            if (isset($invoiceApi['invoiceDelivery']['expectedDelivery'])) $expectedDelivery = $invoiceApi['invoiceDelivery']['expectedDelivery'];
                            $expectedDelivery = Carbon::parse($expectedDelivery)->format('Y-m-d H:i:s');
                            $this->info($expectedDelivery);
                            $invoice->update([
                                'invoiceDelivery' => $invoiceApi['invoiceDelivery'],
                                'expectedDelivery' => $expectedDelivery,
                            ]);
                        }

                        $invoice->update(['status_send' => 10]);
                    } catch (QueryException $exception) {
                        $invoice->update(['status_send' => 0]);
                        Log::debug('Loi', [$exception]);
                        $this->warn($exception->getTraceAsString());
                    }

                }
            });

    }
}
