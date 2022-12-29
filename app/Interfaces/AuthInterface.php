<?php
namespace App\Interfaces;

use App\Http\Requests\AuthRequest;

interface AuthInterface
{
    /**
     * get a JWT via given credentials.
     *
     * @method POST api/auth/login
     * @param \App\Http\Requests\AuthRequest  $request
     * @access public
     */
    public function login(AuthRequest $request);

    public function me();

    public function logout();

    public function refresh();
}
