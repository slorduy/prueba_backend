<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartDetail extends Model
{
    public $timestamps = false;
    protected $table = 'cart_detail';
    public function getProduct()
    {
        return $this->hasMany(Product::class, 'id', 'product_id');
    }
}
