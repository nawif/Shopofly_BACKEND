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

    private $VAT = 0.05;

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
        return $this->belongsTo('App\Transaction');
    }
    public function listings()
    {
        return $this->belongsToMany('App\Listing','orders_listings')->withPivot('quantity');
    }

    public function getBill()
    {
        $total =0;

        foreach ($this->listings as $listing) {
            $quantity = $listing->pivot->quantity;
            $price = (float) filter_var( $listing->price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
            $total += $price * $quantity;
        }
        $bill['total']=round($total, 2);
        $bill['vat']=round($total*$this->VAT, 2);
        $bill['total_with_vat']=round($total*(1+$this->VAT), 2);
        return $bill;
    }



}

