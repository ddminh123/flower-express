<?php

namespace App\Models;

use Carbon\Carbon;
use Encore\Admin\Facades\Admin;
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

    public function scopeToday($query)
    {
        return $query->whereDate('expectedDelivery', Carbon::today());
    }

    public function scopeTomorrow($query)
    {
        return $query->whereDate('expectedDelivery', Carbon::tomorrow());
    }

    public function scopeMe($query)
    {
        return $query->whereHas('items', function ($q) {
            return $q->where('opsFlorist', Admin::user()->id);
        });
    }
}
