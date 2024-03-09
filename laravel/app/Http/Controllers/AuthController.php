<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordReset;

class AuthController extends Controller
{

	use ResponseTrait;

    public function login(LoginRequest $request) {

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->responseError('Invalid login details.', 401);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->responseError('No user found.');
        }

        $tokenCreated = $user->createToken('authToken');

        $data = [
            'access_token' => $tokenCreated->plainTextToken,
            'token_type'   => 'Bearer',
            'user' => $user,
        ];

       	return $this->responseSuccess($data);
    }

    public function register(RegisterRequest $request) {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $tokenCreated = $user->createToken('auth_token');

        $data = [
            'access_token' => $tokenCreated->plainTextToken,
            'token_type'   => 'Bearer',
            'user' => $user,
        ];

        return $this->responseSuccess($data);
    }

    public function resetPassword(Request $request) {

        if ( $request->email) {
            $password = "password".rand(1000,100000);
            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($password);
            $user->save();

            Mail::to($user)->send(new PasswordReset($password));
        }
    }
}
