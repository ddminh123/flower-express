<?php

namespace App\Console\Commands;

use App\Models\KiotVietInvoice;
use App\Models\KiotVietInvoiceDelivery;
use App\Models\KiotVietInvoiceDetail;
use App\Models\KiotVietInvoicePayment;
use App\KiotVietService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;

class SyncInvoiceToday extends Command
{
    protected $service;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:today {day?} {--queue=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dong bo invoice';

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
        try {
            $dateItem = $this->argument('day') ?? now()->format('Y-m-d');
            $this->info($dateItem);
            $data = [
                'createdDate' => $dateItem,
                'SaleChannel' => true,
                'pageSize' => 100
            ];
            $response = Curl::to('https://public.kiotapi.com/invoices')
                ->withHeaders(array('Retailer: '.$this->service->shopCode,'Authorization: Bearer '.$this->service->getAccessToken()))
                ->withData($data)
                ->asJsonResponse(true)
                ->get();

            $count = KiotVietInvoice::query()->where('createdDate','like','%'.$dateItem.'%')->count();
            $totalInvoice = $response['total'];

            if ($count > $totalInvoice) {
                KiotVietInvoice::query()->where('createdDate','like','%'.$dateItem.'%')->delete();
            }

            $pageSize = $response['pageSize'];
            $totalPage = ceil($totalInvoice/$pageSize);

            $currentItem = 0;
            if ($totalPage <= 1) $totalPage = 1;

            for ($i = 1; $i <= $totalPage; $i++)
            {
                $this->info('Page: '.$i);
                $this->info('currentItem: '.$currentItem);
                $data = [
                    'pageSize' => 100,
                    'currentItem' => $currentItem,
                    'createdDate' => $dateItem,
                    'SaleChannel' => true,
                ];
                $response = Curl::to('https://public.kiotapi.com/invoices')
                    ->withHeaders(array('Retailer: '.$this->service->shopCode,'Authorization: Bearer '.$this->service->getAccessToken()))
                    ->withData($data)
                    ->asJsonResponse(true)
                    ->get();

                $customers = $response['data'] ?? [];
                if (is_array($customers) && count($customers)){
                    foreach ($customers as $customer)
                    {
                        $this->info($customer['id']);
                        $verify = KiotVietInvoice::query()->where('id',$customer['id'])->first();

                        if (!$verify) {
                            if (empty($customer['createdDate'])) $customer['createdDate'] = now()->format('Y-m-d H:i:s');
                            if (empty($customer['modifiedDate'])) $customer['modifiedDate'] = now()->format('Y-m-d H:i:s');

                            $invoice = KiotVietInvoice::query()->create($customer);
                        }else{
                            $verify->update($customer);
                            $verify = KiotVietInvoice::query()->where('id',$customer['id'])->first();
                            $invoice = $verify;
                        }

                        $details = $customer['invoiceDetails'];
                        $payments = $customer['payments'] ?? [];
                        $invoiceDelivery = $customer['invoiceDelivery'] ?? [];

                        KiotVietInvoiceDetail::query()->where('invoiceId',$invoice->id)->delete();
                        foreach ($details as $detail){
                            $productInfo = $this->getProductDetail($detail['productId']);
                            $detail['_invoiceId'] = $invoice->_id;
                            $detail['invoiceId'] = $invoice->id;
                            $detail['created_at'] = now()->format('Y-m-d H:i:s');
                            $detail['updated_at'] = now()->format('Y-m-d H:i:s');
                            $detail['category_id'] = $productInfo['category_id'] ?? '';
                            $detail['category_name'] = $productInfo['category_name'] ?? '';
                            $detail['master_code'] = $productInfo['master_code'] ?? '';
                            $detail['trade_mark_name'] = $productInfo['trade_mark_name'] ?? '';//thuong hieu
                            KiotVietInvoiceDetail::query()->insert($detail);
                        }

                        $this->invoicePayment($payments, $invoice);
                        $this->invoiceDelivery($invoiceDelivery, $invoice);
                        $invoice->update(['status_send' => 10]);

                    }
                }

                $currentItem += 100;
            }


            $this->info('done');
        }catch (\Exception $exception){
            $this->error($exception->getMessage());
            $this->service->saveSyncLog($this->getName(),$this->getDescription(),$exception);
            Log::error('MMZ Loi dong bo invoice '.now()->format('d/m/Y H:i:s'),[$exception]);
        }
    }

    private function invoicePayment($paymentFields, KiotVietInvoice $invoice)
    {
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
    }

    private function invoiceDelivery($deliveryField, KiotVietInvoice $invoice)
    {
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
    }

    private function getProductDetail($id)
    {
        $product = $this->service->getProduct($id);
        $data['category_id'] = $product['categoryId'] ?? '';
        $data['category_name'] = $product['categoryName'] ?? '';
        $data['master_code'] = $product['masterCode'] ?? '';
        $data['trade_mark_name'] = $product['tradeMarkName'] ?? '';//thuong hieu

        return $data;
    }
}
