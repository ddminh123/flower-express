<?php

namespace App\Console\Commands;

use App\Models\KiotVietCustomer;
use App\KiotVietService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;

class SyncCustomer extends Command
{
    protected $service;
    protected $page;
    protected $currentItem;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:customer {page?} {current?}';

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
        try {
            $response = Curl::to('https://public.kiotapi.com/customers')
                ->withHeaders(array('Retailer: '.$this->service->shopCode,'Authorization: Bearer '.$this->service->getAccessToken()))
                ->withData([
                    'orderBy' => 'createdDate',
                    'orderDirection' => 'desc'
                ])
                ->asJsonResponse(true)
                ->get();

            $totalInvoice = $response['total'];
            $pageSize = $response['pageSize'];
            $totalPage = ceil($totalInvoice/$pageSize);

            $currentItem = $this->argument('current') ?? 0;
            if ($totalPage <= 1) $totalPage = 1;

            $page = $this->argument('page') ?? 1;
            for ($i = $page; $i <= $totalPage; $i++)
            {
                $this->info('Page: '.$i);
                $this->info('currentItem: '.$currentItem);
                $this->page = $i;
                $this->currentItem = $currentItem;

                $data = [
                    'pageSize' => 100,
                    'currentItem' => $currentItem,
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
                        $customerDB = KiotVietCustomer::query()->where('id',$customer['id'])->first();
                        if (!$customerDB){
                            $this->info('CREATED '.$customer['code']);
                            KiotVietCustomer::query()->insert($customer);
                        }else{
                            $this->info('UPDATED '.$customer['code']);
                            $customerDB->update($customer);
                        }
                    }
                }

                $currentItem += 100;
            }

            $this->info('done');
        }catch (\Exception $exception){
            Log::error('Sync customers page '.$this->page.' current '.$this->currentItem);
        }
    }
}
