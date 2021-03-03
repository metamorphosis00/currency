<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        if (!auth()->attempt($data)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid Credentials']);
        }

        $token = auth()->user()->createToken('authToken')->accessToken;

        return response()->json(['status' => 'success', 'token' => $token]);
    }

    public function register(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        $data['password'] = bcrypt($request->password);

        $user = User::create($data);

        $token = $user->createToken('authToken')->accessToken;

        return response()->json(['status' => 'success', 'token' => $token]);
    }
}
