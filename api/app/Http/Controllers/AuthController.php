<?php

namespace App\Http\Controllers;

use App\Classes\Auth\RegistrationComponent;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Clockwork\Request\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use App\Classes\Auth\LoginComponent;
use Mockery\Exception;


class AuthController extends Controller
{
    public function register(RegistrationRequest $request)
    {
        $data = $request->validated();
        /**
         * RegisterResult is array contains User and token
         */
        $registerResult = (new RegistrationComponent())->register($data);
        return response($registerResult);
    }

    public function login(AuthRequest $request)
    {
        $credential = $request->validated();
        try {
            /**
             * loginResult is array contains User and token
             */
            $loginResult = (new LoginComponent())->login($credential);
        } catch (Exception $e) {
            return response(['error' => $e->getMessage()], $e->getCode());
        }
        return response($loginResult);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();

        return response([
            'success' => true
        ]);
    }
}
