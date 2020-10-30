<?php

namespace App\Console\Commands;

use App\Models\KiotVietInvoice;
use App\Models\KiotVietInvoiceDetail;
use Illuminate\Console\Command;

class InvoiceDetail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:detail';

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
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        KiotVietInvoice::query()->select('_id','id','invoiceDetails')->chunk(500, function ($invoiceChunks){
            $this->info('chunk '.count($invoiceChunks));
            foreach ($invoiceChunks as $invoiceChunk){
                $this->info('invoiceId '.$invoiceChunk->_id);
                $details = $invoiceChunk->invoiceDetails;
                foreach ($details as $detail){
                    $detail['_invoiceId'] = $invoiceChunk->_id;
                    $detail['invoiceId'] = $invoiceChunk->id;
                    $detail['created_at'] = now()->format('Y-m-d H:i:s');
                    $detail['updated_at'] = now()->format('Y-m-d H:i:s');
                    $check = KiotVietInvoiceDetail::query()->where('invoiceId')->count();
                    if (!$check){
                        KiotVietInvoiceDetail::query()->insert($detail);
                    }
                }
            }
        });
    }
}
