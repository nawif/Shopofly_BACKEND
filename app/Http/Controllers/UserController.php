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
use Illuminate\Support\Facades\Hash;

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
            $user = Auth::user();
            $validator = Validator::make($request->all(),[
                'mobile_number' => 'nullable| unique:users,mobile_number,'.$user->id,
                'new_password' => 'nullable| min:8| max:26',
                'old_password' => 'nullable| min:8| max:26',
                'name' => 'nullable',
                'email' => 'nullable|email'

            ]);
            if ($validator->fails()) {
                return new Response(['error'=>"validator", 'cause by' => $validator->messages()->first()],400);
            }

            $user = Auth::user();
            if (!$user)
                return new Response(['error' => 'Token is invalid or expired'], 400);
            $fields = $request->only(['mobile_number', 'old_password', 'new_password', 'name', 'email']);
            if(isset($fields['mobile_number']))
                $user->mobile_number = $fields['mobile_number'];
            if(isset($fields['old_password'], $fields['new_password'])){
                if(!Hash::check($fields['old_password'], $user->password))
                    return new Response(['cause by' => "Passwords didn't match!"], 400);
                $user->password = Hash::make($fields['new_password']);
            }
            if(isset($fields['name']))
                $user->name = $fields['name'];
            if(isset($fields['email']))
                $user->email = $fields['email'];
            $user->save();
            return new Response(['message' => 'Profile Updated!'],200);
        }


        public function authenticate(Request $request){

        }
        public function getUserAddresses(){
            $userAddress=$this->getAuthenticatedUser()->addresses()->get();
            return response()->json($userAddress,200);
        }

        public function addAddress(Request $request) {
            $validator = Validator::make($request->all(),[
                'label' => 'required',
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

            $fields = $request->only(['status', 'city', 'country', 'district', 'street', 'house_number','label']);
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
