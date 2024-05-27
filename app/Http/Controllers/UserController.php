<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function read()
    {
        try {
            $array = ['error' => ''];

            $info = $this->loggedUser;
            $info['avatar'] = url('media/avatars/' . $info['avatar']);
            $array['data'] = $info;

            return $array;
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}
