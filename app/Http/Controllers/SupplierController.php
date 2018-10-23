<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupplierController extends Controller
{
    //
    public function store(Request $request){
        // $user=$this->getAuthenticatedUser();
        $validator = Validator::make($request->all(),[
            'name' => 'required| min:2| max:35',
            'description' => 'min:10| max:500'
        ]);
        if ($validator->fails()) {
            return new Response(['error'=>"validator", 'cause by' => $validator->messages()->first()],400);
       }
        $credentials = $request->only('name','description');
        $user = Supplier::create($credentials);
        if (!$user){
            return new Response(['error'=>"error", 'user' => $user],400);
        }
        return Response::make("Done", 200, "supplier $credentials created");
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
}
