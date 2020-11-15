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

    public function getTotalFloristAttribute()
    {
        //total + THK000002;
        $total = $this->getAttribute('total');
        $surcharge = $this->getAttribute('invoiceOrderSurcharges');
        if (is_array($surcharge) && count($surcharge)) {
            $surcharge = collect($surcharge);
            $surchargeCode = $surcharge->where('surchargeCode', 'THK000002')->first();
            $total = $total + $surchargeCode['surValue'];
        }

        return $total;
    }

    public function items()
    {
        return $this->hasMany(KiotVietInvoiceDetail::class, '_invoiceId', '_id');
    }

    public function customer()
    {
        return $this->belongsTo(KiotVietCustomer::class, 'customerId', 'id');
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
