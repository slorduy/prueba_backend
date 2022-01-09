<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $table = "order";
    public $timestamps = false;
    public function getUser(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getDetail(){
        return $this->hasMany(OrderDetail::class,'order_id','id');
    }
}
