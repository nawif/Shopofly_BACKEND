<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Listing;
use App\ListingImage;
use App\Supplier;
use App\Http\Resources\Listing as ListingResource;
use App\Http\Resources\Supplier as SupplierResource;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class ListingController extends Controller
{
    public function getListing($key){
        $Listing=Listing::where('key', '=', $key)->first();

        if(is_null($Listing)){
            return new Response(["error"=>"unvalid listing key"],400);
        }else
            return new ListingResource($Listing);
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

        public function generateImagesNames(){
        $uniqueKey = substr(base64_encode(mt_rand()), 0, 11);
        $Images = ListingImage::where('image_name', '=', $uniqueKey)->first();
        if ($Images === null) {
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
            'description' => 'max:500',
            'supplier_id' => 'required|exists:suppliers,id'
        ]);
        if ($validator->fails()) {
            return new Response(['error'=>"validator", 'cause by' => $validator->messages()->first()],400);
       }
        $Listing=$request->only('itemName','price', 'quantity', 'description','supplier_id');
        $ListingKey=$this->generateListingKey();
        $Listing['key']=$ListingKey;
        $Listing= Listing::create($Listing);
        return new Response($Listing,201);
    }
    public function addListingImages(Request $request, $key){
        $Listing = Listing::where('key', '=', $key)->first();
        if(!$Listing){
            return new Response("no such listing",400);
        }
        $files = $request->file('photos');
        $folder="public/listingsImages";
        if(!empty($files)) {
            foreach($files as $file) {
                $ListingImage['image_name']=$this->generateImagesNames().".".$file->getClientOriginalExtension();
                $ListingImage['listing_id']=$Listing['id'];
                Storage::put($folder."/".$ListingImage['image_name'],file_get_contents($file));
                if(str_contains($file->getClientOriginalExtension(),["obj","mtl"]))
                    $ListingImage['type'] = "ar";
                ListingImage::create($ListingImage);
            }
        }
        return new Response("image added!",200);
    }

    public function getSupplierListing($id){
        $Supplier = Supplier::where('id', '=', $id)->first();
        if(!$Supplier)
            return new Response(['error' => "unvalid supplier"],400);
        return new Response (ListingResource::collection($Supplier->listings()->get()),200);
    }

    public function getListingAR($key)
    {
        $Listing = Listing::find('key','=',$key);

        
    }
}
