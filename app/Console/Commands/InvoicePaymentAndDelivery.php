<?php

namespace App\Console\Commands;

use App\Models\KiotVietInvoice;
use App\Models\KiotVietInvoiceDelivery;
use App\Models\KiotVietInvoicePayment;
use App\KiotVietService;
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
        $this->checkTable();
        KiotVietInvoice::query()->select('_id', 'id', 'code', 'payments', 'invoiceDelivery', 'status_send')
            ->where('status_send', '<>', 10)->orderByDesc('_id')
            ->chunk(1000, function ($invoices) {
                foreach ($invoices as $invoice) {
                    try {
                        $this->info($invoice->code);

                        $invoiceApi = $this->service->findInvoiceByCode($invoice->code);
                        $paymentFields = [];
                        if ($invoiceApi && !empty($invoiceApi['payments'])) {
                            $invoice->update(['payments' => $invoiceApi['payments']]);
                            $paymentFields = $invoiceApi['payments'];
                        }

                        if (!empty($paymentFields) && is_array($paymentFields) && count($paymentFields)) {
                            foreach ($paymentFields as $paymentField) {
                                $this->info('---' . $paymentField['id']);
                                $paymentField['_invoice_id'] = $invoice->_id;
                                $paymentField['invoice_id'] = $invoice->id;
                                $check = KiotVietInvoicePayment::query()->where('id', $paymentField['id'])->first();
                                if (!$check) {
                                    KiotVietInvoicePayment::query()->create($paymentField);
                                } else {
                                    $check->update($paymentField);
                                }
                                $this->info($invoice->code . ' PAYMENT');
                            }
                        }

                        $deliveryField = [];
                        if ($invoiceApi && !empty($invoiceApi['invoiceDelivery'])) {
                            $invoice->update(['invoiceDelivery' => $invoiceApi['invoiceDelivery']]);
                            $deliveryField = $invoiceApi['invoiceDelivery'];
                        }

                        if (is_array($deliveryField) && count($deliveryField)) {
                            $deliveryField['_invoice_id'] = $invoice->_id;
                            $deliveryField['invoice_id'] = $invoice->id;
                            $check2 = KiotVietInvoiceDelivery::query()->where('invoice_id', $invoice->id)->first();
                            if (!$check2) {
                                KiotVietInvoiceDelivery::query()->create($deliveryField);
                            } else {
                                $check2->update($deliveryField);
                            }
                            $this->info($invoice->code . ' DELIVERY');
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

    private function checkTable()
    {
        try {
            $fields = KiotVietInvoice::query()->select('_id', 'id', 'code', 'payments', 'invoiceDelivery')->whereNotNull('payments')->whereNotNull('invoiceDelivery')->first();
            if ($fields) $fields = $fields->toArray();
            $paymentFields = $fields['payments'] ?? [];
            $deliveryFields = $fields['invoiceDelivery'] ?? [];

            if (!Schema::hasTable('kiotviet_invoice_payments')) {
                if (count($paymentFields)) {
                    Schema::create('kiotviet_invoice_payments', function (Blueprint $table) use ($paymentFields) {
                        $table->bigIncrements('_id');
                        $table->integer('_invoice_id');
                        $table->integer('invoice_id');
                        $table->text('description');
                        foreach ($paymentFields[0] as $key => $paymentField) {
                            if (is_array($paymentField)) {
                                $table->text($key)->nullable();
                            } else {
                                $table->string($key)->nullable();
                            }
                        }
                        $table->timestamps();
                    });
                }
            }

            if (!Schema::hasTable('kiotviet_invoice_delivery')) {
                if (count($deliveryFields)) {
                    Schema::create('kiotviet_invoice_delivery', function (Blueprint $table) use ($deliveryFields) {
                        $table->bigIncrements('_id');
                        $table->integer('_invoice_id');
                        $table->integer('invoice_id');
                        $table->string('weight');
                        $table->string('length');
                        $table->string('width');
                        $table->string('height');
                        foreach ($deliveryFields as $key => $deliveryField) {
                            if (is_array($deliveryField)) {
                                $table->text($key)->nullable();
                            } else {
                                $table->string($key)->nullable();
                            }
                        }
                        $table->timestamps();
                    });
                }
            }
        } catch (\Exception $exception) {
            $this->warn($exception->getMessage());
            Schema::dropIfExists('kiotviet_invoice_payments');
            Schema::dropIfExists('kiotviet_invoice_delivery');
        }

    }
}
