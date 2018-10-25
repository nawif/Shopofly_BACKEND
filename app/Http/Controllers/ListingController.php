<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Listing;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class ListingController extends Controller
{
    public function getListing($key){
        $Listing = Listing::where('key', '=', $key)->first();
        if(!$Listing)
            return new Response(['error' => "unvalid request"],400);

        return new Response($Listing,400);

        $images=$Listing->images;
    }

    public function generateListingKey(){
        $uniqueKey = substr(base64_encode(mt_rand()), 0, 11);
        $Listing = Listing::where('key', '=', $uniqueKey)->first();
        if ($Listing === null) {
            return $uniqueKey;
        }else{
            return $this->generateListingKey();
        }
    }

    public function addListing(Request $request){
        $validator = Validator::make($request->all(),[
            'itemName' => 'required|min:5|max:30',
            'price' => 'required',
            'quantity' => 'required',
            'description' => 'max:50',
            'supplier_id' => 'required|exists:suppliers ,id'
        ]);
        $Listing=$request->only('itemName','price', 'quantity', 'description','supplier_id');
        $ListingKey=$this->generateListingKey();
        $Listing['key']=$ListingKey;
        $Listing= Listing::create($Listing);
        return new Response(['listing' => $Listing],200);
    }

}
