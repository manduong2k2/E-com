<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jeidison\CompositeKey\CompositeKey;

class Cart extends Model
{
    
    use HasFactory,CompositeKey;
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];

    protected $primaryKey = ['user_id','product_id'];
    public $incrementing = false;

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
