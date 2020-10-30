<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KiotVietProduct extends Model
{
    protected $table = 'kiotviet_products';

    protected $guarded = [];

    protected $casts = [
        'attributes' => 'array',
        'units' => 'array',
        'images' => 'array',
        'inventories' => 'array',
        'priceBooks' => 'array',
        'productFormulas' => 'array'
    ];

    protected $primaryKey = '_id';

    public function stocks()
    {
        return $this->hasMany(KiotVietStock::class,'ProductId','id')->orderByDesc('id');
    }
}
