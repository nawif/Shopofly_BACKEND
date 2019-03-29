<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Listing;
use Illuminate\Auth\Access\Response;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\Delivery;

class OrderController extends Controller
{
    //
    public function checkOut(Request $request){
        $orders=$request->only('orders', 'address_id');
        $user = Auth::user();
        // dd($orders['orders'][0]['key']);
        foreach ($orders['orders'] as $order) { // Checking Availablity
            if(!$this->isAvailable($order['key'], $order['quantity']))
                return new Response("Sorry, item is sold out",400);
        }
        foreach ($orders['orders'] as $order) {
            Listing::where('key', '=',$order['key'])->decrement('quantity',$order['quantity']);
            $listing=Listing::where('key', '=',$order['key'])->first();
            $placedOrder = ['address_id' =>$orders['address_id'] , 'quantity' => $order['quantity'],'listing_id' => $listing->id,'user_id'=>$user->id ];
            $placedOrder=Order::create($placedOrder);
        }

        return Response(['orderNumber' => $placedOrder->id],200);
    }

    public function isAvailable($key , $quantity)
    {
        $listing=Listing::where('key', '=',$key)->first();
        if($listing['quantity']>=$quantity)
            return true;
        else
            return false;
    }

    public function getOrderDetails($orderId) {
        $order = Order::find($orderId);
        if (!$order)
            return new Response(['error' => 'Could not find an order with the given order id.'], 400);

        return new Response(['order' => $order], 200);
    }



}
