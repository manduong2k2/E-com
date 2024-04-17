<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jeidison\CompositeKey\CompositeKey;

class RoleUser extends Model
{
    use HasFactory;
    use HasFactory,CompositeKey;
    protected $fillable = [
        'user_id',
        'role_id',
        'quantity',
    ];

    protected $primaryKey = ['user_id','role_id'];
    public $incrementing = false;

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function role(){
        return $this->belongsTo(Role::class);
    }
}
