<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    //
    public function login(Request $request)
{
    $request->validate([
        'username' => 'required|email',
        'password' => 'required'
    ]);
        $response = Http::asForm()->post('https://public-api.wordpress.com/oauth2/token', [

        'client_id' => env('WP_CLIENT_ID'),
        'client_secret' => env('WP_CLIENT_SECRET'),
        'grant_type' => 'password',
        'username' => $request->username,
        'password' => $request->password,
        'redirect_uri' => env('WP_REDIRECT_URI'),
        'code' => env('WP_CODE'),
    ]);

    if ($response->successful()) {
        $token = $response->json()['access_token'];
        session(['wp_token' => $token]);
          return response()->json([
            'message' => 'Login successful',
            'token' => $token
        ]);
    }

    return response()->json(['message' => 'Invalid credentials'], 401);
}
 public function logout(Request $request)
    {
        $request->session()->forget(['wp_user', 'wp_token']);
        return response()->json(['message' => 'Logged out']);
    }
}
