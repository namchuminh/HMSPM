<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'quantity',
        'expired_quantity',
        'damaged_quantity',
        'borrowed_quantity',    
        'status'
    ];

    // Tạo mã sản phẩm tự động nếu không nhập
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->code)) {
                $product->code = strtoupper(Str::random(8));
            }
        });
    }
}
