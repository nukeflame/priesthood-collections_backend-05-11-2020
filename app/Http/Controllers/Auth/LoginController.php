<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Auth;
use App\Http\Resources\Auth\Auth as AuthResource;
use App\Http\Requests\Auth\LoginRequest;
use Laravel\Passport\Client as BaseClient;
use Route;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class LoginController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        return  new AuthResource($user);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // get passport
        $client = BaseClient::where('password_client', 1)->first();
        $request->request->add([
            'grant_type'    => 'password',
            'client_id'     => $client->id,
            'client_secret' => $client->secret,
            'username'      =>  $request->email,
            'password'      =>  $request->password,
            'scope'         => '*'
        ]);

        $credentials = request(['email', 'password']);
        //login user
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => "We couldn't find your credentials in our records."
            ], 401);
        }
        $user = $request->user();
        if (!$user->hasVerifiedEmail()) {
            return  response()->json(['errorCode' => 401, 'user' => new AuthResource($user)], 200);
        }

        // Fire off the internal request.
        $proxy = Request::create(
            'oauth/token',
            'POST'
        );
        $dispatched = Route::dispatch($proxy);
        return $dispatched;
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
