<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    protected $fillable = [
        'id', 
        'name',
        'description',
        'price',
    ];

    protected $table = "product";

    public function getProduct(){
        return $this->belongsTo(Product::class, 'product_id');
    }
}
