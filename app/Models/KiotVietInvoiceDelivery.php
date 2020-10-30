<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KiotVietInvoiceDelivery extends Model
{
    protected $table = 'kiotviet_invoice_delivery';

    protected $primaryKey = '_id';

    protected $guarded = [];

    protected $casts = [
        'partnerDelivery' => 'array'
    ];
}
