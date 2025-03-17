<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'loan_date',
        'return_date',
        'status',
        'quantity',
        'notes'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
