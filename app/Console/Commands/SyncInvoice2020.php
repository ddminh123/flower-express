<?php

namespace App\Console\Commands;

use App\KiotVietService;
use App\Models\KiotVietInvoice;
use App\Models\KiotVietInvoiceDetail;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Ixudra\Curl\Facades\Curl;

class SyncInvoice2020 extends Command
{
    protected $service;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:2020 {startDate?} {endDate?}';

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
        $startOfYear = $this->argument('startDate') ?? now()->subDays(5)->format('Y-m-d');
        $now = $this->argument('endDate') ?? now()->format('Y-m-d');

        $period = CarbonPeriod::create($startOfYear, $now);

        // Iterate over the period
        foreach ($period as $date) {
            $date = $date->format('Y-m-d');
            $this->info($date);

            $data = [
                'lastModifiedFrom' => $date,
            ];
            $response = Curl::to('https://public.kiotapi.com/invoices')
                ->withHeaders(array('Retailer: ' . $this->service->shopCode, 'Authorization: Bearer ' . $this->service->getAccessToken()))
                ->withData($data)
                ->asJsonResponse(true)
                ->get();

            $totalInvoice = $response['total'];
            $pageSize = $response['pageSize'];
            $totalPage = ceil($totalInvoice / $pageSize);

            $currentItem = 0;
            if ($totalPage <= 1) $totalPage = 1;

            for ($i = 1; $i <= $totalPage; $i++) {
                $this->info('Page: ' . $i. '===='.$date);
                $this->info('currentItem: ' . $currentItem. '===='.$date);
                $data = [
                    'pageSize' => 100,
                    'currentItem' => $currentItem,
                    'lastModifiedFrom' => $date,
                    'IncludeInvoiceDelivery' => true
                ];
                $response = Curl::to('https://public.kiotapi.com/invoices')
                    ->withHeaders(array('Retailer: ' . $this->service->shopCode, 'Authorization: Bearer ' . $this->service->getAccessToken()))
                    ->withData($data)
                    ->asJsonResponse(true)
                    ->get();

                $customers = $response['data'];
                if (is_array($customers)) {
                    foreach ($customers as $customer) {
                        $this->info($customer['id']);
                        $expectedDelivery = $customer['purchaseDate'];
                        if (isset($customer['invoiceDelivery']['expectedDelivery'])) $expectedDelivery = $customer['invoiceDelivery']['expectedDelivery'];
                        $expectedDelivery = Carbon::parse($expectedDelivery)->format('Y-m-d H:i:s');
                        $customer['expectedDelivery'] = $expectedDelivery;
                        $verify = KiotVietInvoice::query()->where('id', $customer['id'])->first();
                        if (!$verify) {
                            $invoice = KiotVietInvoice::query()->create($customer);
                            $details = $invoice->invoiceDetails;
                            foreach ($details as $detail) {
                                $detail['_invoiceId'] = $invoice->_id;
                                $detail['invoiceId'] = $invoice->id;
                                $detail['created_at'] = now()->format('Y-m-d H:i:s');
                                $detail['updated_at'] = now()->format('Y-m-d H:i:s');
                                $check = KiotVietInvoiceDetail::query()->where('invoiceId',$invoice->id)->count();
                                if (!$check) {
                                    KiotVietInvoiceDetail::query()->insert($detail);
                                }
                            }
                        }else{
                            $verify->update($customer);
                        }
                    }
                }

                $currentItem += 100;
            }

        }

        $this->info('done');
    }
}
