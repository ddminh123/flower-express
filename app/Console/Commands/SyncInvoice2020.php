<?php

namespace App\Console\Commands;

use App\Models\KiotVietInvoice;
use App\Models\KiotVietInvoiceDetail;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Ixudra\Curl\Facades\Curl;

class SyncInvoice2020 extends Command
{
    protected $urlApi = 'https://id.kiotviet.vn/connect/token';
    protected $shopCode;
    protected $clientId;
    protected $clientSecret;
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
        $this->shopCode = 'stmmz';// env('KV_SHOP_CODE','stmmz');
        $this->clientId = 'ac678706-31cd-4fd2-b4d4-8bfea24c9840';//env('KV_API_CLIENT_ID','ac678706-31cd-4fd2-b4d4-8bfea24c9840');
        $this->clientSecret = '3422A62852F8349E54B741127B53AC79164D2DA6';//env('KV_API_SECRET','3422A62852F8349E54B741127B53AC79164D2DA6');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = Carbon::createFromDate(2020, 7, 8);

        $startOfYear = $this->argument('startDate') ?? $date->copy()->startOfYear()->format('Y-m-d');
        $now = $this->argument('endDate') ?? $date->format('Y-m-d');

        $period = CarbonPeriod::create($startOfYear, $now);

        // Iterate over the period
        foreach ($period as $date) {
            $date = $date->format('Y-m-d');
            $this->info($date);

            $data = [
                'createdDate' => $date,
            ];
            $response = Curl::to('https://public.kiotapi.com/invoices')
                ->withHeaders(array('Retailer: ' . $this->shopCode, 'Authorization: Bearer ' . $this->getAccessToken()['access_token']))
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
                    'createdDate' => $date,
                ];
                $response = Curl::to('https://public.kiotapi.com/invoices')
                    ->withHeaders(array('Retailer: ' . $this->shopCode, 'Authorization: Bearer ' . $this->getAccessToken()['access_token']))
                    ->withData($data)
                    ->asJsonResponse(true)
                    ->get();

                $customers = $response['data'];
                if (is_array($customers)) {
                    foreach ($customers as $customer) {
                        $this->info($customer['id']);
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
                        }
                    }
                }

                $currentItem += 100;
            }

        }

        $this->info('done');
    }

    private function getAccessToken()
    {
        $data = [
            'scopes' => 'PublicApi.Access',
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret
        ];

        $response = Curl::to('https://id.kiotviet.vn/connect/token')
            ->withContentType('application/x-www-form-urlencoded')
            ->withData($data)
            ->asJsonResponse(true)
            ->post();
        return $response;
    }
}
