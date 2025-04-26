<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'customer_name',
        'phone_number',
        'address',
        'order_date',
        'pickup_date',
        'total',
        'payment',
        'change',
        'quantity',
        'note',
        'payment_status',
        'payment_method',
        'dp_amount'
    ];

    public function details() {
        return $this->hasMany(TransactionDetail::class, 'transaction_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class,);
    }

}
