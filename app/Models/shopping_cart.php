<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class shopping_cart extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'product_id', 'size', 'quantity'];

    // Quan hệ với bảng users
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với bảng products
    public function product()
    {
        return $this->belongsTo(Products::class);
    }
}
