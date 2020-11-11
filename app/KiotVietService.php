<?php


namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;

class KiotVietService
{
    protected $urlApi = 'https://id.kiotviet.vn/connect/token';
    public $shopCode;
    public $clientId;
    public $clientSecret;

    public function __construct()
    {
        $this->shopCode = 'hoatuoilt2';
        $this->clientId = '4aeda44a-71cc-413d-92cd-e266e9ecceca';
        $this->clientSecret = 'C412B7FCDB03FE4C3818D99673D49633775838F6';
    }

    public function saveSyncLog($job,$name, $note = [])
    {
        Log::info('saveSyncLog',[
            'date_time' => now()->format('Y-m-d H:i:s'),
            'job' => $job,
            'name' => $name,
            'note' => $note
        ]);
    }

    public function isRateLimited($response)
    {
        return (isset($response['responseStatus']['errorCode']) && $response['responseStatus']['errorCode'] == 'RateLimited') ? true : false;
    }

    public function isTokenExpired($response)
    {
        return (isset($response['responseStatus']['errorCode']) && $response['responseStatus']['errorCode'] == 'TokenException') ? true : false;
    }

    public function delayJobIfApiRateLimited($name, $param = [])
    {
        return Artisan::queue($name,$param)->delay(now()->addMinutes(30));
    }

    public function getAccessToken()
    {
        if (Cache::has('flower-token')){
            $token = Cache::get('flower-token');
        }else{
            try {
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
                $token = $response['access_token'];
                Cache::put('flower-token',$token,now()->addDays(1));
            }catch (\Exception $exception){
                Log::error('Loi khong lay duoc access_token',[$exception]);
                $this->saveSyncLog('getAccessToken','getAccessToken',$exception);
            }
        }

        return $token;
    }

    public function getProduct($id)
    {
        if (Cache::has('product-'.$id)){
            $product = Cache::get('product-'.$id);
        }else{
            $product = Curl::to('https://public.kiotapi.com/products/'.$id)
                ->withHeaders(array('Retailer: '.$this->shopCode,'Authorization: Bearer '.$this->getAccessToken()))
                ->asJson()
                ->asJsonResponse(true)
                ->get();
            Cache::put('product-'.$id,$product, now()->addMinutes(5));
        }
        return $product;
    }

    public function totalInvoiceDate($date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        $data = [
            'createdDate' => $date,
            'pageSize' => 10
        ];

        $response = Curl::to('https://public.kiotapi.com/invoices')
            ->withHeaders(array('Retailer: ' . $this->shopCode, 'Authorization: Bearer ' . $this->getAccessToken()))
            ->withData($data)
            ->asJsonResponse(true)
            ->get();

        return $response['total'] ?? null;
    }

    public function totalCustomerDate($date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        $data = [
            'lastModifiedFrom' => $date,
            'pageSize' => 10
        ];

        $response = Curl::to('https://public.kiotapi.com/customers')
            ->withHeaders(array('Retailer: ' . $this->shopCode, 'Authorization: Bearer ' . $this->getAccessToken()))
            ->withData($data)
            ->asJsonResponse(true)
            ->get();

        return $response['total'] ?? null;
    }

    public function findInvoiceByCode($code)
    {
        return Curl::to('https://public.kiotapi.com/invoices/code/' . $code)
            ->withData([
                'SaleChannel' => true
            ])
            ->withHeaders(array('Retailer: ' . $this->shopCode, 'Authorization: Bearer ' . $this->getAccessToken()))
            ->asJsonResponse(true)
            ->get();
    }

    public function findInvoiceById($code)
    {
        return Curl::to('https://public.kiotapi.com/invoices/' . $code)
            ->withData([
                'SaleChannel' => true
            ])
            ->withHeaders(array('Retailer: ' . $this->shopCode, 'Authorization: Bearer ' . $this->getAccessToken()))
            ->asJsonResponse(true)
            ->get();
    }

    public function findCustomerByCode($code)
    {
        return Curl::to('https://public.kiotapi.com/customers/code/' . $code)
            ->withHeaders(array('Retailer: ' . $this->shopCode, 'Authorization: Bearer ' . $this->getAccessToken()))
            ->asJsonResponse(true)
            ->get();
    }

    public function getUser()
    {
        return Curl::to('https://public.kiotapi.com/users')
            ->withHeaders(array('Retailer: ' . $this->shopCode, 'Authorization: Bearer ' . $this->getAccessToken()))
            ->withData(['pageSize' => 100])
            ->asJsonResponse(true)
            ->get();
    }

    /**
     * @param $id
     * @param $price
     * @param $deliveryCode
     * {
     * "id": 7199,
     * "bankName": "EXIMBANK DN",
     * "accountNumber": "101514851026665",
     * "retailerId": 28688,
     * "modifiedDate": "2020-02-21T11:04:25.9500000",
     * "createdDate": "2017-03-15T20:25:26.5070000"
     * },
     * {
     * "id": 7314,
     * "bankName": "VCB CN",
     * "accountNumber": "0071001212285",
     * "retailerId": 28688,
     * "modifiedDate": "2020-05-13T11:26:14.3500000",
     * "createdDate": "2017-07-24T11:38:40.0570000"
     * }
     * @return mixed
     */
    public function updateInvoice($id, $price, $deliveryCode)
    {
        $data = [
            "soldById" => 451334, //fix cung Ngoc Trinh
            "codPaymentMethod" => "Tranfer",    //Đây là khai báo phương thức thanh toán gồm Cash(tiền mặt) Card (Thẻ) Tranfer (Chuyển khoản)
            "codPaymentAccount" => 8346,
            "deliveryDetail" => [  // Đây là nội dung Đơn giao hàng
                "status" => 3, // Trạng thái 3 là chỉ đã giao thành công
                "usingPriceCod" => true,   // Giá trị là true nghĩa Đơn giao hàng có thu COD
                "priceCodPayment" => $price,    // Đây là giá trị thanh toán theo giá trị Thu hộ COD
                "partnerDelivery" => [   //Đây là thông tin đối tác giao hàng
                    "code" => $deliveryCode,    //Đây là Mã đối tác giao hàng
                    "name" => $deliveryCode
                ]
            ]
        ];

        return Curl::to('https://public.kiotapi.com/invoices/' . $id)
            ->withHeaders(array('Retailer: ' . $this->shopCode, 'Authorization: Bearer ' . $this->getAccessToken()))
            ->withData($data)
            ->asJson()
            ->asJsonResponse(true)
            ->put();
    }

    /**
     * @param array $dataInvoiceKv
     * @param double $price
     * @return mixed
     */
    public function updateInvoiceTest($dataInvoiceKv, $price)
    {
        $invoiceDelivery = $dataInvoiceKv['invoiceDelivery'] ?? [];
        $invoiceId = $dataInvoiceKv['id'];

        $data = [
            "status" => 1,
            "soldById" => 451334, //fix cung Ngoc Trinh
            "codPaymentMethod" => "Transfer",    //Đây là khai báo phương thức thanh toán gồm Cash(tiền mặt) Card (Thẻ) Tranfer (Chuyển khoản)
            "codPaymentAccount" => 8346,
            "deliveryDetail" => [  // Đây là nội dung Đơn giao hàng
                "receiver" => $invoiceDelivery['receiver'] ?? '',
                "contactNumber" => $invoiceDelivery['contactNumber'] ?? '',
                "address" => $invoiceDelivery['address'] ?? '',
                "locationId" => $invoiceDelivery['locationId'] ?? '',
                "locationName" => $invoiceDelivery['locationName'] ?? '',
                "wardId" => $invoiceDelivery['wardId'] ?? '',
                "wardName" => $invoiceDelivery['wardName'] ?? '',
                "weight" => $invoiceDelivery['weight'] ?? '',
                "length" => $invoiceDelivery['length'] ?? '',
                "width" => $invoiceDelivery['width'] ?? '',
                "height" => $invoiceDelivery['height'] ?? '',
                "status" => 3, // Trạng thái 3 là chỉ đã giao thành công
                "usingPriceCod" => true,   // Giá trị là true nghĩa Đơn giao hàng có thu COD
                "priceCodPayment" => $price,    // Đây là giá trị thanh toán theo giá trị Thu hộ COD
                "partnerDelivery" => [   //Đây là thông tin đối tác giao hàng
                    "code" => $invoiceDelivery['partnerDelivery']['code'],    //Đây là Mã đối tác giao hàng
                    "name" => $invoiceDelivery['partnerDelivery']['name']
                ]
            ]
        ];

        return Curl::to('https://public.kiotapi.com/invoices/' . $invoiceId)
            ->withHeaders(array('Retailer: ' . $this->shopCode, 'Authorization: Bearer ' . $this->getAccessToken()))
            ->withData($data)
            ->asJson()
            ->asJsonResponse(true)
            ->put();
    }
}
