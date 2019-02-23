<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListingSpecification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'listing_id', 'type', 'key', 'value'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    public function listing()
    {
        return $this->belongsTo('App\Listing');
    }

}
