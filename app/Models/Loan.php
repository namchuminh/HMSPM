<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'loan_date',
        'return_date',
        'status',
        'notes'
    ];
}
