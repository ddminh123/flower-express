<?php

namespace App\Console\Commands;

use App\Models\KiotVietCustomer;
use App\KiotVietService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;

class SyncCustomerToday extends Command
{
    protected $service;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:today {day?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dong bo customer';

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
            $dateItem = $this->argument('day') ?? now()->subDays(1)->format('Y-m-d');

            $this->info($dateItem);
            $data = [
                'lastModifiedFrom' => $dateItem,
                'pageSize' => 100
            ];

            $response = Curl::to('https://public.kiotapi.com/customers')
                ->withHeaders(array('Retailer: '.$this->service->shopCode,'Authorization: Bearer '.$this->service->getAccessToken()))
                ->withData([$data])
                ->asJsonResponse(true)
                ->get();

            $totalInvoice = $response['total'];
            $pageSize = $response['pageSize'];
            $totalPage = ceil($totalInvoice/$pageSize);

            $currentItem = 0;
            if ($totalPage <= 1) $totalPage = 1;

            $page = 1;
            for ($i = $page; $i <= $totalPage; $i++)
            {
                $this->info('Page: '.$i);
                $this->info('currentItem: '.$currentItem);
                $data = [
                    'pageSize' => 100,
                    'currentItem' => $currentItem,
                    'lastModifiedFrom' => $dateItem,
                ];
                $response = Curl::to('https://public.kiotapi.com/customers')
                    ->withHeaders(array('Retailer: '.$this->service->shopCode,'Authorization: Bearer '.$this->service->getAccessToken()))
                    ->withData($data)
                    ->asJsonResponse(true)
                    ->get();

                $customers = $response['data'];
                if (is_array($customers)){
                    foreach ($customers as $customer)
                    {
                        $this->info($customer['id']);
                        $check = KiotVietCustomer::query()->where('id',$customer['id'])->first();
                        if (!$check){
                            KiotVietCustomer::query()->insert($customer);
                        }
                    }
                }

                $currentItem += 100;
            }

            $this->info('done');
        }catch (\Exception $exception){
            $this->error($exception->getMessage());
            $this->service->saveSyncLog($this->getName(),$this->getDescription(),$exception);
            Log::error('flower loi dong bo customer '.now()->format('d/m/Y H:i:s'),[$exception]);
        }
    }
}
