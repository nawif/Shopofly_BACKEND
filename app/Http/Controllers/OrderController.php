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
use App\Classes\Qrcode;
use GuzzleHttp;

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
        $token = "Bearer "."eyJhbGciOiJSUzI1NiIsImtpZCI6IjZCN0FDQzUyMDMwNUJGREI0RjcyNTJEQUVCMjE3N0NDMDkxRkFBRTEiLCJ0eXAiOiJKV1QiLCJ4NXQiOiJhM3JNVWdNRnY5dFBjbExhNnlGM3pBa2ZxdUUifQ.eyJuYmYiOjE1NTUxMTA3NTcsImV4cCI6MTU1NTExNDM1NywiaXNzIjoiaHR0cHM6Ly9sb2dpbi5oYWxhbGFoLnNhLyIsImF1ZCI6WyJodHRwczovL2xvZ2luLmhhbGFsYWguc2EvcmVzb3VyY2VzIiwic2NvcGVfZXh0X29yZGVyX2d3X2FwaSJdLCJjbGllbnRfaWQiOiJoYWxhbGFoX29yZGVyc19zYXVkIiwic2NvcGUiOlsic2NvcGVfZXh0X29yZGVyX2d3X2FwaSJdfQ.hnHrW9q4XBk1U8ZvTCvbSND2ArX6VkrGcnT5UQkQAQ80-gGNYW3Iq7xelT7cBpRFlRLrH421PG0Q69fQ07usDuvFKOrXVGWpWZQLTF9TVDBd2yYigvac3czLfVXv7RvP4X2_KX2C1yobdqbNNTDbBMN96N9SfqVwvvMUjAwUDRu2kAO-CqoUORsveZxjw-50tK3OqB0YOqSd9ppsH2er_xzioe_IM82cl3JLt7uJTTInUl_z-7l4My0vBB9n4dHJ6yLW0wlxtH-LHzmzds1l48g_4nS_yrWTKALJhjnrR9-NZ0YJjHn8MXUarYKkJr53zS9BPm9aOrfe7I3InwuwBw";
        $link= "https://apigw.halalah.sa/Orders/v2/Order/".env("HALALAH_TERMINAL_ID")."/".$order->getHalalahCode();
        $headers = ['Content-Type' => 'application/json','Authorization' => $token];
        $r = $client->request('GET', $link, ['headers' => $headers]);
        $status = intval($r->getStatusCode());
        return Response($status==200 . '',$status);
    }

    public function getHalalahToken()
    {
        $client = new GuzzleHttp\Client();
        $link= " https://login.halalah.sa/connect/token";
        $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];

    }



}
