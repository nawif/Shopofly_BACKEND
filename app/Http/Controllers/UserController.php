<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    //
        // register a user
        public function store(Request $request){
            $validator = Validator::make($request->all(),[
                'name' => 'required| min:2| max:35',
                'email' => 'required| unique:users| min:2| max:35',
                'mobile_number' => 'required| min:10| max:15| unique:users',
                'address' => 'min:2| max:50',
                'password' => 'required| min:6| max:20'
            ]);

            if ($validator->fails()) {
                return new Response(['error'=>"validator", 'cause by' => $validator->messages()->first()],400);
           }

                $credentials = $request->only('name','email','password','mobile_number','address');
                $credentials['password'] = \Hash::make($credentials['password']);
                
                if($user = User::create($credentials)){ // if user regestration is successful, method will return null otherwise
                    return new Response(['Messeage'=>"user registered"],200);
                }
                else
                    return new Response(['error'=>"error", 'user' => $userData],400);
    
        }
        public function login(Request $request){

        }

}
