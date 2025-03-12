<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLifecycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'previous_status',
        'new_status',
        'changed_by',
        'changed_at'
    ];
}
