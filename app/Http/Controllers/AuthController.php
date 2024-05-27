<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['create', 'login', 'unauthorized']]);
    }

    public function create(Request $request)
    {
        try {
            $array = ['error' => ''];

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                // 'password_confirm' => 'required|same:password'
            ]);

            if (!$validator->fails()) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' =>  Hash::make($request->password)
                ]);

                $token = auth()->attempt([
                    'email' => $request->email,
                    'password' => $request->password
                ]);

                if (!$token) {
                    $array['error'] = 'Invalid credentials';
                } else {
                    $user->avatar = url('media/avatars/' . $user->avatar);
                    $array['user'] = $user;
                    $array['token'] = $token;
                }
            } else {
                $array['error'] = $validator->errors()->first();
            }

            return $array;
        } catch (\Throwable $th) {
            return ['error' => $th->getMessage()];
        }
    }

    public function login(Request $request)
    {
        try {
            $array = ['error' => ''];

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if (!$validator->fails()) {
                $token = auth()->attempt([
                    'email' => $request->email,
                    'password' => $request->password
                ]);

                if (!$token) {
                    $array['error'] = 'Usuário e/ou senha incorretos';
                } else {
                    $user =  auth()->user();
                    $user->avatar = url('media/avatars/' . $user->avatar);
                    $array['user'] = $user;
                    $array['token'] = $token;
                }
            } else {
                $array['error'] = $validator->errors()->first();
            }

            return $array;
        } catch (\Throwable $th) {
            return ['error' => $th->getMessage()];
        }
    }

    public function logout()
    {
        try {
            auth()->logout();
            return ['error' => ''];
        } catch (\Throwable $th) {
            return ['error' => $th->getMessage()];
        }
    }

    public function refresh()
    {
        try {
            /** @var Illuminate\Auth\AuthManager */
            $auth = auth();

            $user = $auth->user();
            $user['avatar'] = url('media/avatars/' . $user['avatar']);

            return [
                'data' => $user,
                'token' => $auth->refresh(),
            ];
        } catch (\Throwable $th) {
            return ['error' => $th->getMessage()];
        }
    }

    public function unauthorized()
    {
        try {
            return response()->json([
                'error' => 'Não autorizado'
            ], 401);
        } catch (\Throwable $th) {
            return ['error' => $th->getMessage()];
        }
    }
}
