<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transcation extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'user_id','price', 'date'
    ];
    
}