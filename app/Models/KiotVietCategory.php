<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KiotVietCategory extends Model
{
    protected $table = 'kiotviet_categories';

    protected $guarded = [];

    protected $casts = [
        'children' => 'array'
    ];
}
