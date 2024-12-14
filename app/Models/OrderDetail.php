<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'order_id',
        'product_id',
        'size',
        'quantity'
    ];
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
        // return $this->hasOne(products::class, 'id', 'product_id');
    }
    public function order()
    {
        return $this->belongsTo(order::class, 'order_id', 'id');
    }
}
