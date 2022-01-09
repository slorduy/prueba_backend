<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cart extends Model
{
    public $timestamps = false;
    protected $table = 'cart';
    public function getDetails()
    {
        return $this->hasMany(CartDetail::class, 'cart_id', 'id');
    }
}
