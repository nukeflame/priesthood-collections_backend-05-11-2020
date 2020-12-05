<?php

namespace App\Http\Resources\Register;

use Illuminate\Http\Resources\Json\JsonResource;

class Register extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $acess = $this->createToken('PriestHood Password Grant Client');
        $accessToken = $acess->accessToken;
        $token = $acess->token;
        $expiresIn = time($token->expires_at);

        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'access_token' => $accessToken,
            'expires_in' => $expiresIn,
        ];
    }
}
