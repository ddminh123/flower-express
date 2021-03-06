<?php

namespace App\Console\Commands;

use App\Models\KiotVietInvoice;
use App\Models\KiotVietInvoiceDelivery;
use App\Models\KiotVietInvoiceDetail;
use App\Models\KiotVietInvoicePayment;
use App\KiotVietService;
use Carbon\Carbon;
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
                'lastModifiedFrom' => $dateItem
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
                    'lastModifiedFrom' => $dateItem,
                    'IncludeInvoiceDelivery' => true
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
                        $expectedDelivery = $customer['purchaseDate'];
                        if (isset($customer['invoiceDelivery']['expectedDelivery'])) $expectedDelivery = $customer['invoiceDelivery']['expectedDelivery'];
                        $expectedDelivery = Carbon::parse($expectedDelivery)->format('Y-m-d H:i:s');
                        $customer['expectedDelivery'] = $expectedDelivery;

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

                        KiotVietInvoiceDetail::query()->where('invoiceId',$invoice->id)->delete();
                        foreach ($details as $detail){
                            $detail['_invoiceId'] = $invoice->_id;
                            $detail['invoiceId'] = $invoice->id;
                            $detail['expectedDelivery'] = $expectedDelivery;
                            $detail['created_at'] = now()->format('Y-m-d H:i:s');
                            $detail['updated_at'] = now()->format('Y-m-d H:i:s');
                            KiotVietInvoiceDetail::query()->insert($detail);
                        }

                        $invoice->update(['status_send' => 10]);

                    }
                }

                $currentItem += 100;
            }


            $this->info('done');
        }catch (\Exception $exception){
            $this->error($exception->getMessage());
            $this->service->saveSyncLog($this->getName(),$this->getDescription(),$exception);
            Log::error('flower Loi dong bo invoice '.now()->format('d/m/Y H:i:s'),[$exception]);
        }
    }
}
