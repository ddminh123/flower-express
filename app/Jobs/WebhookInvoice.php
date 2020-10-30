<?php

namespace App\Jobs;

use App\Webhook;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WebhookInvoice implements ShouldQueue
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
        $webhookId = $this->webhookId;
        $webhook = Webhook::query()->find($this->webhookId);
        $webhookData = json_decode($webhook->data, true);
        $data = $webhookData['Notifications'][0]['Data'] ?? [];

        if (count($data)){
            foreach ($data as $dataInvoice){
                $invoiceCode = $dataInvoice['Code'];
                dispatch(new WebhookInvoiceItem($invoiceCode,$dataInvoice,$webhookId))->onQueue('webhook');
            }
        }
    }
}
