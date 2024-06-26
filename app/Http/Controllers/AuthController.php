<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Import the User model
use Illuminate\Support\Facades\Hash; // Import the Hash facade
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string', // Konfirmasi kata sandi
        ]);

        try {
            $user = new User();
            $user->name = $request['name'];
            $user->email = $request['email'];
            $user->password = Hash::make($request['password']);
            $user->save();

            $response = ['status' => 200, 'message' => 'Register berhasil'];
        } catch (Exception $e) {
            $response = ['status' => 500, 'message' => $e->getMessage()];
        }

        return response()->json($response);
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function check(Request $request) //cek Auth
    {
        try {
            $user = $request->user();

            if ($user) {
                $response = ['status' => 200, 'message' => 'Success', 'data' => $user];
            } else {
                $response = ['status' => 404, 'message' => 'User not found'];
            }
        } catch (Exception $e) {
            $response = ['status' => 500, 'message' => $e->getMessage()];
        }

        return response()->json($response);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        $response = [
            'success' => true,
            'message' => 'Berhasil Logout'
        ];
        return response($response, 200);
    }
}
