<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'delivery_agent_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];
}
