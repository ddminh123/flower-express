<?php

namespace App\Models;

use App\InvoiceEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class KiotVietInvoiceDetail extends Model
{
    const STATUS_NEW = 0;
    const STATUS_PROGRESS = 1;
    const STATUS_DONE = 2;

    protected $table = 'kiotviet_invoice_details';

    protected $casts = [
        'opsImages' => 'array'
    ];

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

    public function florist()
    {
        return $this->belongsTo(User::class, 'opsFlorist', 'id')->withDefault([
            'name' => ''
        ]);
    }

    public function shipper()
    {
        return $this->belongsTo(User::class, 'opsShipper', 'id')->withDefault([
            'name' => ''
        ]);
    }

    public function scopeToday($query)
    {
        return $query->whereHas('invoice', function ($q) {
           return $q->whereDate('expectedDelivery', Carbon::today());
        });
    }

    public function scopeTomorrow($query)
    {
        return $query->whereHas('invoice', function ($q) {
            return $q->whereDate('expectedDelivery', Carbon::tomorrow());
        });
    }

    public function scopeMe($query)
    {
        return $query->where('opsFlorist', \Admin::user()->id);
    }

    public function getStatusTextAttribute()
    {
        $status = InvoiceEnum::getStatus();

        return $status[$this->opsStatus] ?? $status[0];
    }

    public function getStatusProgressAttribute()
    {
        $statusCurrent = $this->getAttribute('opsStatus');
        $total = count(InvoiceEnum::getStatus()) - 1;


        return $statusCurrent/($total) * 100;
    }
}
