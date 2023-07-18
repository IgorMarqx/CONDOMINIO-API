<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function unauthorized()
    {
        return response()->json([
            'error' => 'Não autorizado',
        ], 401);
    }

    public function register(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'cpf' => ['required', 'digits:11', 'unique:users,cpf,'],
            'password' => ['required'],
            'password_confirmation' => ['required', 'same:password'],
        ]);

        if (!$validator->fails()) {
            $name = $request->input('name');
            $email = $request->input('email');
            $cpf = $request->input('cpf');
            $password = $request->input('password');

            $hash = Hash::make($password);

            $newUser = User::create([
                'name' => $name,
                'email' => $email,
                'cpf' => $cpf,
                'password' => $hash
            ]);
            $newUser->save();

            $token = Auth::attempt([
                'cpf' => $cpf,
                'password' => $password,
            ]);

            if (!$token) {
                $array['error'] = 'Ocorreu um erro.';
                return $array;
            }

            $array['token'] = $token;

            $user = Auth::user();
            $array['user'] = $user;

            $properties = Unit::select(['id', 'name'])
                ->where('id_owner', $user['id'])
                ->get();

            $array['user']['properties'] = $properties;
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function login(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'cpf' => ['required', 'digits:11'],
            'password' => ['required']
        ]);

        if (!$validator->fails()) {
            $cpf = $request->input('cpf');
            $password = $request->input('password');

            $token = Auth::attempt([
                'cpf' => $cpf,
                'password' => $password,
            ]);

            if (!$token) {
                $array['error'] = 'Cpf ou senha inválidos.';
                return $array;
            }

            $array['token'] = $token;

            $user = Auth::user();
            $array['user'] = $user;

            $properties = Unit::select(['id', 'name'])
                ->where('id_owner', $user['id'])
                ->get();

            $array['user']['properties'] = $properties;
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function validateToken()
    {
        $array = ['error' => ''];

        $user = Auth::user();
        $array['user'] = $user;

        $properties = Unit::select(['id', 'name'])
            ->where('id_owner', $user['id'])
            ->get();

        $array['user']['properties'] = $properties;

        return $array;
    }

    public function logout()
    {
        $array = ['error' => ''];

        Auth()->logout();

        return $array;
    }
}
