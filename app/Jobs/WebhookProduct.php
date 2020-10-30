<?php

namespace App\Jobs;

use App\KiotVietProduct;
use App\Webhook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WebhookProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $webhookId;
    /**
     * Create a new job instance.
     * @param $webhookId
     * @return void
     */
    public function __construct($webhookId)
    {
        $this->webhookId = $webhookId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $webhook = Webhook::query()->find($this->webhookId);
        $webhookData = json_decode($webhook->data, true);
        $data = $webhookData['Notifications'][0]['Data'] ?? [];

        if (count($data)){
            foreach ($data as $dataProduct){
                $productIdKv = $dataProduct['Id'];
                $check = KiotVietProduct::query()->where('id',$productIdKv)->first();
                if ($check){
                    $check->update([
                        'code' => $dataProduct['Code'] ?? $check->code,
                        'name' => $dataProduct['Name'] ?? $check->name,
                        'fullName' => $dataProduct['FullName'] ?? $check->fullName,
                        'categoryId' => $dataProduct['CategoryId'] ?? $check->categoryId,
                        'categoryName' => $dataProduct['CategoryName'] ?? $check->categoryName,
                        'masterProductId' => $dataProduct['masterProductId'] ?? $check->masterProductId,
                        'allowsSale' => $dataProduct['AllowsSale'] ?? $check->allowsSale,
                        'hasVariants' => $dataProduct['HasVariants'] ?? $check->hasVariants,
                        'basePrice' => $dataProduct['BasePrice'] ?? $check->basePrice,
                        'weight' => $dataProduct['Weight'] ?? $check->weight,
                        'unit' => $dataProduct['Unit'] ?? $check->unit,
                        'masterUnitId' => $dataProduct['MasterUnitId'] ?? $check->masterUnitId,
                        'conversionValue' => $dataProduct['ConversionValue'] ?? $check->conversionValue,
                        'modifiedDate' => $dataProduct['ModifiedDate'] ?? $check->modifiedDate,
                        'attributes' => $dataProduct['Attributes'] ?? $check->attributes,
                        'units' => $dataProduct['Units'] ?? $check->units,
                        'inventories' => $dataProduct['Inventories'] ?? $check->inventories,
                        'priceBooks' => $dataProduct['PriceBooks'] ?? $check->priceBooks,
                        'images' => $dataProduct['Images'] ?? $check->images,
                    ]);
                    $webhook->update(['note' => 'Cập nhật thông tin sản phẩm '.$productIdKv]);
                }else{
                    KiotVietProduct::query()->create([
                        'id' => $dataProduct['Id'] ?? '',
                        'code' => $dataProduct['Code'] ?? '',
                        'name' => $dataProduct['Name'] ?? '',
                        'fullName' => $dataProduct['FullName'] ?? '',
                        'categoryId' => $dataProduct['CategoryId'] ?? '',
                        'categoryName' => $dataProduct['CategoryName'] ?? '',
                        'masterProductId' => $dataProduct['masterProductId'] ?? '',
                        'allowsSale' => $dataProduct['AllowsSale'] ?? '',
                        'hasVariants' => $dataProduct['HasVariants'] ?? '',
                        'basePrice' => $dataProduct['BasePrice'] ?? '',
                        'weight' => $dataProduct['Weight'] ?? '',
                        'unit' => $dataProduct['Unit'] ?? '',
                        'masterUnitId' => $dataProduct['MasterUnitId'] ?? '',
                        'conversionValue' => $dataProduct['ConversionValue'] ?? '',
                        'modifiedDate' => $dataProduct['ModifiedDate'] ?? '',
                        'attributes' => $dataProduct['Attributes'] ?? '',
                        'units' => $dataProduct['Units'] ?? '',
                        'inventories' => $dataProduct['Inventories'] ?? '',
                        'priceBooks' => $dataProduct['PriceBooks'] ?? '',
                        'images' => $dataProduct['Images'] ?? '',
                    ]);
                    $webhook->update(['note' => 'Thêm mới sản phẩm '.$productIdKv]);
                }
            }
        }
    }
}
