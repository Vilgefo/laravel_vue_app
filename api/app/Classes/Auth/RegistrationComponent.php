<?php

namespace App\Classes\Auth;

use App\Models\User;

class RegistrationComponent
{

    /**
     * @param array $registerData
     * @return array
     */
    public function register(array $registerData): array
    {
        ['name' => $name, 'email' => $email, 'password' => $password] = $registerData;
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password)
        ]);
        $token = $user->createToken('main')->plainTextToken;
        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
