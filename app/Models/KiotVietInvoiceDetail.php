<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KiotVietInvoiceDetail extends Model
{
    const STATUS_NEW = 0;
    const STATUS_PROGRESS = 1;
    const STATUS_DONE = 2;

    protected $table = 'kiotviet_invoice_details';

    protected $guarded = [];

    protected $primaryKey = '_id';

    public function invoice()
    {
        return $this->belongsTo(KiotVietInvoice::class, 'invoiceId', 'id');
    }

    public function product()
    {
        return $this->belongsTo(KiotVietProduct::class, 'productId', 'id');
    }
}
