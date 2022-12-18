<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $this->validate($request, [
            'username' => 'required|min:6|max:20',
            'password' => 'required|min:8|max:30',
        ]);

        $loginType = filter_var($data['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            'password' => $data['password'],
            $loginType => $data['username'],
        ];

        if (! $token = auth('api')->attempt($credentials)) {
            return $this->msgResponse('Unauthorized', 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|min:3|max:50',
            'password' => 'required|min:8|max:30|confirmed',
            'password_confirmation' => 'required|min:8|max:30',
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'username' => 'required|regex:/^[A-Za-z0-9_]+$/|min:6|max:20|unique:users,username,NULL,id,deleted_at,NULL',
        ], [
            'username.regex' => 'The username must contain only letters, numbers, or the underscore.'
        ]);

        unset($data['password_confirmation']);

        try {
            DB::beginTransaction();

            User::create($data);

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->msgResponse($exception->getMessage(), 500);
        }

        return $this->msgResponse('Register successful.', 201);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return $this->msgResponse('Successfully logged out');
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
