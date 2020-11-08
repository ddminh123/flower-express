<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function florist()
    {
        return $this->belongsTo(User::class, 'opsFlorist', 'id')->withDefault([
            'name' => ''
        ]);
    }

    public function scopeToday($query)
    {
        return $query->whereHas('invoice', function ($q) {
           return $q->whereDate('purchaseDate', Carbon::today());
        });
    }

    public function scopeTomorrow($query)
    {
        return $query->whereHas('invoice', function ($q) {
            return $q->whereDate('purchaseDate', Carbon::tomorrow());
        });
    }

    public function scopeMe($query)
    {
        return $query->where('opsFlorist', \Admin::user()->id);
    }

    public function getStatusTextAttribute()
    {
        $status = [
            0 => 'Chưa làm',
            1 => 'Đang làm',
            2 => 'Đã xong',
        ];

        return $status[$this->status] ?? $status[0];
    }
}
