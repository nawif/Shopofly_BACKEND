<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Listing;

class ListingController extends Controller
{
    //
    public function getListing(Request $request){
        $ListingID=$request->only('id');
        Listing::
    }
}
