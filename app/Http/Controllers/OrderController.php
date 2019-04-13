<?php

namespace App\Http\Controllers;
use App\Http\Resources\Order as OrderResource;
use Illuminate\Http\Request;
use App\Listing;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\Delivery;
use App\Transaction;
use App\User;
use App\Classes\Qrcode;
use GuzzleHttp;
use Illuminate\Http\Response;

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

        return Response($order, 200);
    }


    public function getUserOrders(){
        $orders = Auth::user()->orders();
        if($orders){
            $orders = OrderResource::collection($orders->get());
            return Response($orders, 200);
        }else
            return Response("user didn't place any orders", 400);
    }

    public function processTransection(Request $request){
        $card=$request['card_hash'];
        if($card){
            $order = Order::find($request['order_id']);
            $transaction = $order->transaction()->first();
            if($transaction->status == $transaction->statusTypes[2]){
                return Response(["message" => 'Order already been payed for'],400);
            }
            $transaction->update(['status' => $transaction->statusTypes[1]]);
            $transaction->update(['status' => $transaction->statusTypes[2]]);
            return Response(["message" => 'Payment completed!'],200);
        }else
            return Response(["message" => 'Order could be found'],400);

    }
    public function getHalalahQRC($id)
    {
        $order = Order::find($id);
        return Response($order->getHalalahCode(), 200);
    }

    public function getHalalahBillStatus($id){
        $order = Order::find($id)->first();
        $client = new GuzzleHttp\Client();
        $token = "Bearer ".$this->getHalalahToken()['access_token'];
        $link= "https://apigw.halalah.sa/Orders/v2/Order/".env("HALALAH_TERMINAL_ID")."/".$order->getHalalahCode();
        $headers = ['Content-Type' => 'application/json','Authorization' => $token];
        $r = $client->request('GET', $link, ['headers' => $headers]);
        $status = intval($r->getStatusCode());
        $result = ['paid' => $status==200];
        return new Response($result, $status);
    }

    public function getHalalahToken()
    {
        $client = new GuzzleHttp\Client();
        $link= "https://login.halalah.sa/connect/token";
        $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
        $body = [
            'grant_type' => 'client_credentials',
            'client_id' => env("HALALAH_CLIENT_ID"),
            'client_secret' => env('HALALAH_CLIENT_SECRET'),
            'scope' => 'scope_ext_order_gw_api',
        ];
        $response = $client->request('POST', $link, ['form_params' => $body, 'headers' => $headers]);
        return json_decode($response->getBody(), true);

    }



}
