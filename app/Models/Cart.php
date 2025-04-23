<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart'; // Nama tabel dalam database
    protected $fillable = [
        'user_id',
        'customer_id',
        'customer_name',
        'phone_number',
        'address',
        'pickup_date',
        'total',
        'payment_status',
        'payment_method',
        'dp_amount',
        'quantity',
        'note',
    ];

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details() {
        return $this->hasMany(CartDetail::class, 'cart_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
