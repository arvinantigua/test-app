<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{

	use ResponseTrait;

    public function update(UpdateUserRequest $request) {
        
        $password = $request->password ?? null;

        $user = [
            "name" => $request->name,
            "email" => $request->email,
        ];

        if ($password) {
            $user['password'] = Hash::make($request->password);
        }

        if ($request->user()->update($user)) {
            $data = [
                'access_token' => $request->bearerToken(),
                'token_type'   => 'Bearer',
                'user' => $request->user(),
            ];
            return $this->responseSuccess($data);
        } else {
            return $this->responseError('User update unsuccessful.');
        }
    }
}
