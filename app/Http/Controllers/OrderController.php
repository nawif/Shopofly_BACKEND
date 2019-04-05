<?php

namespace App\Http\Controllers;
use App\Http\Resources\Order as OrderResource;
use Illuminate\Http\Request;
use App\Listing;
use Illuminate\Auth\Access\Response;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\Delivery;
use App\Transaction;
use App\User;

class OrderController extends Controller
{
    //
    public function checkOut(Request $request){
        $orders=$request->only('orders', 'address_id');
        foreach ($orders['orders'] as $order) { // Checking availability
            if(!$this->isAvailable($order['key'], $order['quantity']))
                return new Response("Sorry, item is sold out",400);
        }
        
        $transaction = Transaction::create();
        $user = Auth::user();
        $deliveryAgent = User::where('type','=',1)->first();
        if(!$deliveryAgent)
            return Response('no avaliable drivers',400);
        $placedOrder = Order::create([
            'user_id' => $user->id,
            'address_id' => $orders['address_id'],
            'delivery_agent_id' => $deliveryAgent->id,
            'transaction_id' => $transaction->id
        ]);

        foreach ($orders['orders'] as $order) {
            $listing=Listing::where('key', '=' ,$order['key'])->first();
            $listing->decrement('quantity',$order['quantity']);
            // dd($listing);
            $placedOrder->listings()->save($listing,['quantity' => $order['quantity']]);
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
        $order = new OrderResource(Order::find($orderId));

        if (!$order)
            return Response(['error' => 'Could not find an order with the given order id.'], 400);

        return Response(['order' => $order], 200);
    }


    public function getUserOrders(){
        $orders = Auth::user()->orders()->get(); 
        $orders = OrderResource::collection();
        return Response($orders, 200);

    }



}
