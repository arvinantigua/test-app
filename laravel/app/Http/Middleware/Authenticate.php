<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;

class Authenticate extends Middleware
{
    use ResponseTrait;

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return Response
     */
    protected function unauthenticated($request, array $guards) {
        abort($this->responseError("Unauthenticated.", 401));
    }
}
