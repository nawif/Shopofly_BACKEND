<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status','user_id','address_id','delivery_agent_id', 'transaction_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function address()
    {
        return $this->belongsTo('App\Address');
    }

    public function deliveryAgent()
    {
        return $this->belongsTo('App\User','delivery_agent_id');
    }

    public function transaction()
    {
        return $this->hasOne('App\Transaction');
    }
    public function listings()
    {
        return $this->belongsToMany('App\Listing','orders_listings')->withPivot('quantity');
    }




}

