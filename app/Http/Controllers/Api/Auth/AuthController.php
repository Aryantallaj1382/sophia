<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return api_response([],'email or password is wrong' , 400);
        }
//        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return api_response([
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'login success');
    }
    public function p_login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user->professor)
        {
            return api_response([],'you are not a professor', 400);
        }

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return api_response([],'email or password is wrong' , 400);
        }
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return api_response([
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'login success');
    }

    public function loginOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user ) {
            return api_response([],'email is wrong');
        }

        $cacheKey = 'otp:' . $request->email;
        $cachedOtp = Cache::get($cacheKey);

        if (!$cachedOtp) {
            return response()->json([
                'message' => 'The verification code has expired'
            ], 400);
        }

        if ($cachedOtp['code'] != $request->otp) {
            return response()->json([
                'message' => 'The verification code is incorrect'
            ], 400);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        return api_response([
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'login success');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'referral_code' => 'nullable|string',
            'otp' => 'required|string',
            'birth_date' => 'nullable|date',
        ]);

        $cacheKey = 'otp:' . $request->email;
        $cachedOtp = Cache::get($cacheKey);

        if (!$cachedOtp) {
            return response()->json([
                'message' => 'The verification code has expired'
            ], 400);
        }

        if ($cachedOtp['code'] != $request->otp) {
            return response()->json([
                'message' => 'The verification code is incorrect'
            ], 400);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'birth_date' => $request->birth_date,
            'referral_code' => $request->referral_code,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ]);

        Cache::forget($cacheKey);

        $token = $user->createToken('auth_token')->plainTextToken;
        Auth::login($user);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'message' => 'Registration completed successfully'
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return api_response([],'logout success');
    }

}
