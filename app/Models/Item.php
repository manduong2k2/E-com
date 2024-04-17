<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jeidison\CompositeKey\CompositeKey;

class Item extends Model
{
    use HasFactory,CompositeKey;
    protected $fillable = [
        'order_id', 
        'product_id', 
        'cost', 
        'rating', 
        'quantity'
    ];

    protected $primaryKey = ['order_id','product_id'];
    public $incrementing = false;

    public function order(){
        return $this->belongsTo(Order::class);
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
