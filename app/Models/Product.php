<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category_id', 'price', 'stock', 'image'];

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
