<?php

namespace App\Console\Commands;

use App\Models\KiotVietProduct;
use App\KiotVietService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;

class SyncProduct extends Command
{
    protected $service;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dong bo product';

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
            $response = Curl::to('https://public.kiotapi.com/products')
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
                $response = Curl::to('https://public.kiotapi.com/products')
                    ->withHeaders(array('Retailer: '.$this->service->shopCode,'Authorization: Bearer '.$this->service->getAccessToken()))
                    ->withData($data)
                    ->asJsonResponse(true)
                    ->get();

                $customers = $response['data'];
                foreach ($customers as $customer)
                {
                    $this->info($customer['id']);
                    $check = KiotVietProduct::query()->where('id',$customer['id'])->first();
                    if (!$check){
                        KiotVietProduct::query()->create($customer);
                    }else{
                        $check->update($customer);
                        $this->info('UPDATED');
                    }
                }

                $currentItem += 100;
            }

            $this->info('done');
        }catch (\Exception $exception){
            $this->error($exception->getMessage());
            $this->service->saveSyncLog($this->getName(),$this->getDescription(),$exception);
            Log::error('flower loi dong bo product '.now()->format('d/m/Y H:i:s'),[$exception]);
        }
    }
}
