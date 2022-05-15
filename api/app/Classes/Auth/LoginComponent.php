<?php

namespace App\Classes\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;

class LoginComponent
{
    /**
     * @param array $credential
     * @return array
     */
    public function login(array $credential): array
    {
        ['email' => $email, 'password' => $password] = $credential;
        $remember = $credential['remember'] ?? false;
        unset($credential['remember']);

        if(!Auth::attempt($credential, $remember)){
            Throw new Exception('The provided credentials are not correct', 422);
        };
        $user = Auth::user();
        $token = $user->createToken('main')->plainTextToken;
        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
