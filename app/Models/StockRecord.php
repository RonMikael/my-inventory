<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'stock_id',
        'quantity_change',
        'action',
        'stock_room',
        'location',
    ];

    
}
