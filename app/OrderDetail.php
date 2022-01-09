<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    
    protected $table = "order_detail";
    public $timestamps =  false;
    public function getProduct(){
        return $this->hasMany(Product::class,'id' , 'product_id');
    }
    
}
