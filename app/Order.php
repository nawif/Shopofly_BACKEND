<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Classes\Qrcode;

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

    public function getHalalahCode(){
        // dd($this->transaction->id);
        $inputs = array(
            "merchant_category_code"=> env("HALALAH_MERCHANT_CATEGORY_CODE"),
            "merchant_name"=> env("HALALAH_MERCHANT_NAME"),
            "merchant_city"=> env("HALALAH_MERCHANT_CITY"),
            "postal_code"=> env("HALALAH_MERCHANT_POSTAL"),
            "merchant_name_ar"=> env("HALALAH_MERCHANT_NAME_AR"),
            "merchant_city_ar"=> env("HALALAH_MERCHANT_CITY_AR"),
            "amount"=> $this->getBill()['total_with_vat'],
            "bill"=> $this->transaction->id,
            "reference"=> $this->id.$this->created_at,
            "terminal"=> env("HALALAH_TERMINAL")
        );

        $qrcode = new Qrcode($inputs);
        return  $qrcode->output();

    }



}

