<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'itemName', 'price', 'quantity','supplier_id',"key", "description"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function supplier()
    {
        return $this->belongsTo('App\Supplier');
    }

    public function images()
    {
        return($this->hasMany('App\ListingImage')->where('type','=','image')->get());
    }

    public function ArObject()
    {
        return($this->hasMany('App\ListingImage')->where('type','=','ar')->get());
    }

    public function reviews()
    {
        return $this->hasMany('App\Review');
    }

    public function orders()
    {
        return $this->hasMany('App\Order');
    }

    public function specifications()
    {
        return $this->hasMany('App\ListingSpecification');
    }

    public function orderedQuantity()
    {
        return $this->belongsToMany('App\Listing','orders_listings')->withPivot('quantity');
    }



}
