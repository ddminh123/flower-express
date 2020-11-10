<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KiotVietInvoice extends Model
{
    protected $table = 'kiotviet_invoices';

    protected $primaryKey = '_id';

    protected $guarded = [];

    protected $casts = [
        'payments' => 'array',
        'invoiceOrderSurcharges' => 'array',
        'invoiceDetails' => 'array',
        'SaleChannel' => 'array',
        'invoiceDelivery' => 'array'
    ];

    public function items()
    {
        return $this->hasMany(KiotVietInvoiceDetail::class,'_invoiceId','_id');
    }

    public function customer()
    {
        return $this->belongsTo(KiotVietCustomer::class,'customerId', 'id');
    }
}
