<?php

namespace App\Repositories;

use App\Http\Controllers\Api\ApiResponse;
use App\Http\Requests\AuthRequest;
use App\Http\Resources\AuthResource;
use App\Interfaces\AuthInterface;
use Illuminate\Support\Facades\Auth;
use stdClass;

class AuthRepository implements AuthInterface
{
    /**
     * Get a JWT token via given credentials.
     *
     * @param  \App\Http\Requests\AuthRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AuthRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if ($token = $this->guard()->attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return ApiResponse::successItem(new AuthResource($this->guard()->user()));
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();
        return ApiResponse::successItem(new stdClass, 'Successfully logged out');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return ApiResponse::successItem($this->guard()->refresh(), 'Successfully logged out');
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $data = [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $this->guard()->factory()->getTTL() * 24
        ];
        return ApiResponse::successItem($data, 'Successfully logged in');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
