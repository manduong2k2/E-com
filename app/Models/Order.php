<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'user_id',
        'total',
    ];
    public function products(){
        return $this->hasMany(Product::class);
    }
    public function items(){
        return $this->hasMany(Item::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
