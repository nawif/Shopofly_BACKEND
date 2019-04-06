<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Order as OrderResource;


class DeliveryController extends Controller
{
    //
    public function getAgentAssignedOrders(){
        $user = Auth::user();
        $orders = $user->deliveries();
        if($orders){
            $orders = OrderResource::collection($orders->get());
            return Response($orders, 200);               
        }else
            return Response("user didn't place any orders", 400);
    }

    
}
