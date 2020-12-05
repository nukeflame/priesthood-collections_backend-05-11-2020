<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\User;
use App\Models\Order;
use App\Models\VerifyUser;
use Hash;
use App\Http\Resources\Auth\Auth as AuthResource;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;
use App\Notifications\Auth\VerifyEmailNotification;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = new User();
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->access_portal = 0;
        $user->password = Hash::make($request->password);
        $user->save();
        $user->customers()->attach(4);//customer role
        //
        VerifyUser::create([
            'user_id' => $user->id,
            'token' => (string) Str::uuid(),
            'expires_at' => Carbon::now()->addHours(2),
            'otp' => mt_rand(100000, 999999)
        ]);
        //
        $user->notify(new VerifyEmailNotification($user));
        return  response()->json(new AuthResource($user));
    }

    public function authenticate(Request $request)
    {
        $verifyUser = VerifyUser::where('token', $request->token)->first();
        $user = $verifyUser->user;
        if (isset($verifyUser)) {
            return  response()->json(new AuthResource($user));
        } else {
            $user->delete();
            return  response()->json(['error' => 'Token not found'], 404);
        }
    }

    public function updateOtp(Request $request, $id)
    {
        $verifyUser = VerifyUser::find($id);
        if (isset($verifyUser)) {
            $verifyUser->otp = mt_rand(100000, 999999);
            $verifyUser->update();
            $user = $verifyUser->user;
            // send mail
            $user->notify(new VerifyEmailNotification($user));
            return  response()->json(new AuthResource($user));
        } else {
            return  response()->json(['error' => 'Token not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->update();
        $user->customers()->attach(4);//customer role
        
        $user->notify(new VerifyEmailNotification($user));
        return  response()->json(new AuthResource($user));
    }

    public function verifyUser(Request $request)
    {
        $verifyUser = VerifyUser::where('token', $request->token)->first();
        $message = [];
        if (isset($verifyUser)) {
            $user = $verifyUser->user;
            if ($user->hasVerifiedEmail()) {
                $message = ['verified' => 'Your e-mail is verified'];
                return response()->json($message, 422);
            } else {
                if (boolval((int) $verifyUser->otp === (int) $request->otpNo)) {
                    $user->markEmailAsVerified();
                    $verifyUser->delete();
                    //auth token
                    $acess = $user->createToken('PriestHood Password Grant Client');
                    $accessToken = $acess->accessToken;
                    $token = $acess->token;
                    $expiresIn = time($token->expires_at);
                    $data = [
                        'userData' => new AuthResource($user),
                        'access_token' => $accessToken,
                        'expires_in' => $expiresIn,
                        'token_type' =>  "Bearer"
                    ];

                    return response()->json($data);
                } else {
                    $message = ['error' => 'Your OTP is invalid'];
                    return response()->json($message, 422);
                }
            }
        }
    }
}
