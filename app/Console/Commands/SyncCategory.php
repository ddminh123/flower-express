<?php

namespace App\Console\Commands;

use App\Models\KiotVietCategory;
use App\Models\KiotVietInvoice;
use App\KiotVietService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;

class SyncCategory extends Command
{
    protected $service;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dong bo category';

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
            $response = Curl::to('https://public.kiotapi.com/categories')
                ->withHeaders(array('Retailer: '.$this->service->shopCode,'Authorization: Bearer '.$this->service->getAccessToken()))
                ->withData([])
                ->asJsonResponse(true)
                ->get();

            $totalInvoice = $response['total'];
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
                ];
                $response = Curl::to('https://public.kiotapi.com/categories')
                    ->withHeaders(array('Retailer: '.$this->service->shopCode,'Authorization: Bearer '.$this->service->getAccessToken()))
                    ->withData($data)
                    ->asJsonResponse(true)
                    ->get();

                $customers = $response['data'];
                foreach ($customers as $customer)
                {
                    $this->info($customer['categoryId']);
                    $check = KiotVietCategory::query()->where('categoryId',$customer['categoryId'])->first();
                    if (!$check){
                        KiotVietCategory::query()->create($customer);
                    }
                }

                $currentItem += 100;
            }

            $this->info('done');
        }catch (\Exception $exception){
            $this->error($exception->getMessage());
            $this->service->saveSyncLog($this->getName(),$this->getDescription(),$exception);
            Log::error('MMZ loi dong bo category '.now()->format('d/m/Y H:i:s'),[$exception]);
        }
    }
}
