<?php

namespace App\Jobs;

use App\KiotVietInvoice;
use App\KiotVietInvoiceDetail;
use App\KiotVietService;
use App\Webhook;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class WebhookInvoiceItem implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $invoiceCode;
    public $data;
    public $webhookId;

    /**
     * Create a new job instance.
     * @param string $invoiceCode
     * @param array $data;
     * @param string $webhookId;
     * @return void
     */
    public function __construct($invoiceCode, $data, $webhookId)
    {
        $this->invoiceCode = $invoiceCode;
        $this->data = $data;
        $this->webhookId = $webhookId;
    }

    public function tags()
    {
        return [$this->invoiceCode];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $invoiceCode = $this->invoiceCode;
        $data = $this->data;
        $webhook = Webhook::query()->find($this->webhookId);
        $note = $webhook->note;

        $invoice = KiotVietInvoice::query()->where('code',$invoiceCode)->first();

        $keyed['id'] = $data['Id'];
        $keyed['code'] = $data['Code'];
        $keyed['purchaseDate'] = Carbon::parse($data['PurchaseDate'])->format('Y-m-d H:i:s');
        $keyed['branchId'] = $data['BranchId'];
        $keyed['branchName'] = $data['BranchName'];
        $keyed['soldById'] = $data['SoldById'];
        $keyed['soldByName'] = $data['SoldByName'];
        $keyed['customerId'] = $data['CustomerId'] ?? 0;
        $keyed['customerCode'] = $data['CustomerCode'] ?? '';
        $keyed['customerName'] = $data['CustomerName'] ?? '';
        $keyed['total'] = $data['Total'];
        $keyed['totalPayment'] = $data['TotalPayment'];
        $keyed['status'] = $data['Status'];
        $keyed['statusValue'] = $data['StatusValue'];
        $keyed['usingCod'] = $data['UsingCod'];
        $keyed['invoiceDetails'] = $data['InvoiceDetails'];
        $keyed['modifiedDate'] = isset($data['ModifiedDate']) ? Carbon::parse($data['ModifiedDate'])->format('Y-m-d H:i:s') : Carbon::now()->format('Y-m-d H:i:s');
        $keyed['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $keyed['status_send'] = 1;//type
        $keyed['discount'] = $data['Discount'] ?? '';//type
        $keyed['discountRatio'] = $data['DiscountRatio'] ?? '';
        $keyed['payments'] = $data['Payments'];
        $keyed['invoiceDelivery'] = $data['InvoiceDelivery'];

        $products = $data['InvoiceDetails'];//invoice products
        if ($invoice){
            $invoiceId = $invoice->_id;
            $invoiceKvId = $invoice->id;
            $invoice->update($keyed);
            foreach ($products as $product){
                $productInfo = $this->getProductDetail($product['ProductId']);
                $check = KiotVietInvoiceDetail::query()->where('invoiceId',$invoiceId)->where('productId',$product['ProductId'])->first();
                if ($check){
                    $check->update([
                        'productId' => $product['ProductId'] ?? '',
                        'productCode' => $product['ProductCode'] ?? '',
                        'productName' => $product['ProductName'] ?? '',
                        'quantity' => $product['Quantity'] ?? '',
                        'price' => $product['Price'] ?? '',
                        'discount' => $product['Discount'] ?? '',
                        'branch_id' => $data['BranchId'] ?? '',
                        'branch_name' => $data['BranchName'] ?? '',
                        'category_id' => $productInfo['category_id'] ?? '',
                        'category_name' => $productInfo['category_name'] ?? '',
                        'master_code' => $productInfo['master_code'] ?? '',
                        'trade_mark_name' => $productInfo['trade_mark_name'] ?? '',//thuong hieu
                    ]);
                }else{
                    KiotVietInvoiceDetail::query()->create([
                        '_invoiceId' => $invoiceId,
                        'invoiceId' => $invoiceKvId,
                        'productId' => $product['ProductId'] ?? '',
                        'productCode' => $product['ProductCode'] ?? '',
                        'productName' => $product['ProductName'] ?? '',
                        'quantity' => $product['Quantity'] ?? '',
                        'price' => $product['Price'] ?? '',
                        'discount' => $product['Discount'] ?? '',
                        'subTotal' => $product['SubTotal'] ?? '',
                        'branch_id' => $data['BranchId'] ?? '',
                        'branch_name' => $data['BranchName'] ?? '',
                        'category_id' => $productInfo['category_id'] ?? '',
                        'category_name' => $productInfo['category_name'] ?? '',
                        'master_code' => $productInfo['master_code'] ?? '',
                        'trade_mark_name' => $productInfo['trade_mark_name'] ?? '',//thuong hieu
                    ]);
                }
            }
            $webhook->update(['note' => $note. 'Cập nhật thông tin hóa đơn '.$invoiceCode.' thành công,']);
        }else{
            $keyed['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
            $keyed['createdDate'] = Carbon::now()->format('Y-m-d H:i:s');
            $invoice = KiotVietInvoice::query()->create($keyed);//them moi invoice
            $invoiceProducts = collect();
            foreach ($products as $product){
                $productInfo = $this->getProductDetail($product['ProductId']);
                $invoiceProducts->push([
                    '_invoiceId' => $invoice->_id,
                    'invoiceId' => $invoice->id,//id cua kv
                    'productId' => $product['ProductId'] ?? '',
                    'productCode' => $product['ProductCode'] ?? '',
                    'productName' => $product['ProductName'] ?? '',
                    'subTotal' => $product['SubTotal'] ?? '',
                    'quantity' => $product['Quantity'] ?? '',
                    'price' => $product['Price'] ?? '',
                    'discount' => $product['Discount'] ?? '',
                    'branch_id' => $data['BranchId'] ?? '',
                    'branch_name' => $data['BranchName'] ?? '',
                    'category_id' => $productInfo['category_id'] ?? '',
                    'category_name' => $productInfo['category_name'] ?? '',
                    'master_code' => $productInfo['master_code'] ?? '',
                    'trade_mark_name' => $productInfo['trade_mark_name'] ?? '',//thuong hieu
                ]);
            }

            foreach ($invoiceProducts->chunk(200) as $ivps){
                DB::table('kiotviet_invoice_details')->insert($ivps->toArray());
            }
            $webhook->update(['note' => $note. 'Thêm mới hóa đơn '.$invoiceCode.' thành công,']);
        }
    }

    private function getProductDetail($id)
    {
        $service = new KiotVietService();
        $product = $service->getProduct($id);
        $data['category_id'] = $product['categoryId'] ?? '';
        $data['category_name'] = $product['categoryName'] ?? '';
        $data['master_code'] = $product['masterCode'] ?? '';
        $data['trade_mark_name'] = $product['tradeMarkName'] ?? '';//thuong hieu

        return $data;
    }
}
