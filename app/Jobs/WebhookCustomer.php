<?php

namespace App\Jobs;

use App\KiotVietCustomer;
use App\Webhook;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WebhookCustomer implements ShouldQueue
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
            foreach ($data as $dataCustomer){
                $customerCode = $dataCustomer['Id'];
                $check = KiotVietCustomer::query()->where('id',$customerCode)->first();
                if ($check){
                    $check->update([
                        'code' => $dataCustomer['Code'] ?? $check->code,
                        'name' => $dataCustomer['Name'] ?? $check->name,
                        'gender' => $dataCustomer['Gender'] ?? $check->gender,
                        'birthDate' => $dataCustomer['BirthDate'] ?? $check->birthDate,
                        'contactNumber' => $dataCustomer['ContactNumber'] ?? $check->contactNumber,
                        'address' => $dataCustomer['Address'] ?? $check->address,
                        'locationName' => $dataCustomer['LocationName'] ?? $check->locationName,
                        'email' => $dataCustomer['Email'] ?? $check->email,
                        'modifiedDate' => $dataCustomer['ModifiedDate'] ?? $check->modifiedDate,
                        'type' => $dataCustomer['Type'] ?? $check->type,
                        'organization' => $dataCustomer['Organization'] ?? $check->organization,
                        'taxCode' => $dataCustomer['TaxCode'] ?? $check->taxCode,
                        'comments' => $dataCustomer['Comments'] ?? $check->comments,
                    ]);
                    $webhook->update(['note' => 'Cập nhật thông tin khách hàng '.$customerCode]);
                }else{
                    KiotVietCustomer::query()->create([
                        'id' => $dataCustomer['Id'] ?? '',
                        'code' => $dataCustomer['Code'] ?? '',
                        'name' => $dataCustomer['Name'] ?? '',
                        'gender' => $dataCustomer['Gender'] ?? '',
                        'birthDate' => $dataCustomer['BirthDate'] ?? '',
                        'contactNumber' => $dataCustomer['ContactNumber'] ?? '',
                        'address' => $dataCustomer['Address'] ?? '',
                        'locationName' => $dataCustomer['LocationName'] ?? '',
                        'email' => $dataCustomer['Email'] ?? '',
                        'modifiedDate' => $dataCustomer['ModifiedDate'] ?? '',
                        'type' => $dataCustomer['Type'] ?? '',
                        'organization' => $dataCustomer['Organization'] ?? '',
                        'taxCode' => $dataCustomer['TaxCode'] ?? '',
                        'comments' => $dataCustomer['Comments'] ?? '',
                    ]);
                    $webhook->update(['note' => 'Thêm mới khách hàng '.$customerCode]);
                }
            }
        }
    }
}
