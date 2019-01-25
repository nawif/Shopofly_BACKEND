<?php
// @codingStandardsIgnoreStart

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class UserController extends Controller
{
    //
        /* *
        register a user
        */
        public function store(Request $request){
            $validator = Validator::make($request->all(),[
                'mobile_number' => 'required| unique:users',
                'password' => 'required| min:8| max:26'
            ]);
            if ($validator->fails()) {
                return new Response(['error'=>"validator", 'cause by' => $validator->messages()->first()],400);
           }
            $credentials = $request->only('password','mobile_number');
            $credentials['password'] = \Hash::make($credentials['password']);
            $user = User::create($credentials);
            if (!$user){
                return new Response(['error'=>"error", 'user' => $user],400);
            }
            $token = JWTAuth::fromUser($user);
            return response()->json(compact('user','token'),201);
        }
        public function authenticate(Request $request){

        }

        public function getAuthenticatedUser()
        {
                try {

                        if (! $user = JWTAuth::parseToken()->authenticate()) {
                                return response()->json(['user_not_found'], 404);
                        }
                } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                        return response()->json(['token_expired'], $e->getStatusCode());

                } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                        return response()->json(['token_invalid'], $e->getStatusCode());

                } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                        return response()->json(['token_absent'], $e->getStatusCode());
                }

                return response()->json(compact('user'));
        }
        public function isLoggedIn(){
            $user=$this->getAuthenticatedUser();
            return new Response(['error'=>"error", 'user' => $userData],400);


        }



}
