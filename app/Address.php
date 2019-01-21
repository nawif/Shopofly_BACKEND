<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','status','city','country','district','street','house_number'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];
}
