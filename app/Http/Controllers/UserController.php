<?php
// @codingStandardsIgnoreStart

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Address;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
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


        // get user details
        public function show() {
            $user = Auth::user();
            if (!$user)
                return new Response(['error' => 'Token is invalid or expired'], 400);
            return new Response(['user' => $user], 200);
        }

        public function update(Request $request) {
            $validator = Validator::make($request->all(),[
                'mobile_number' => 'required| unique:users',
                'password' => 'required| min:8| max:26',
                'name' => 'nullable',
                'email' => 'nullable|email'

            ]);
            if ($validator->fails()) {
                return new Response(['error'=>"validator", 'cause by' => $validator->messages()->first()],400);
            }

            $user = Auth::user();
            if (!$user)
                return new Response(['error' => 'Token is invalid or expired'], 400);
            $fields = $request->only(['mobile_number', 'password', 'name', 'email']);
            $user->mobile_number = $request->mobile_number;
            $user->password = $request->password;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();
        }


        public function authenticate(Request $request){

        }
        //TODO: SECURITY CHECKS, ERROR HANDLING
        public function getUserAddresses(){
            $userAddress=$this->getAuthenticatedUser()->addresses()->get();
            return response()->json($userAddress,200);
        }

        public function addAddress(Request $request) {
            $validator = Validator::make($request->all(),[
                'status' => 'required',
                'city' => 'required',
                'country' => 'required',
                'district' => 'required',
                'street' => 'required',
                'house_number' => 'required'
            ]);
            if ($validator->fails()) {
                return new Response(['error'=>"validator", 'cause by' => $validator->messages()->first()],400);
            }

            $user = Auth::user();
            if (!$user)
                return new Response(['error'=>"validator", 'cause by' => $validator->messages()->first()],400);
            
            $fields = $request->only(['status', 'city', 'country', 'district', 'street', 'house_number']);
            $fields['user_id'] = $user->id;
            $address = Address::create($fields);
            if (!$address)
                return new Response(['error' => 'Could not create address, please try again.'], 400);
            
            return new Response(['address' => $address], 200);

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
                $token = JWTAuth::getToken();
                $user = JWTAuth::toUser($token);

                return $user;
        }
        
        public function isLoggedIn(){
            $user=$this->getAuthenticatedUser();
            return new Response(['error'=>"error", 'user' => $user],400);


        }



}
