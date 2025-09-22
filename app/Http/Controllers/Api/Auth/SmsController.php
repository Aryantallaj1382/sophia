<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Cache;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Support\Facades\Log;


class SmsController extends Controller
{

    public function sendOtp(Request $request)
    {
        $data = $request->validate([
            'email' => 'nullable|email',
        ]);

        $identifier = $data['email'] ?? $data['mobile'] ?? $data['user'];
        $isEmail = Str::contains($identifier, '@');

        $cacheKey = 'otp:' . $identifier;

        $cachedOtp = Cache::get($cacheKey);
        $cooldown = 120;

        if ($cachedOtp && isset($cachedOtp['sent_at'])) {
            $sentAt = \Carbon\Carbon::parse($cachedOtp['sent_at']);
            $secondsPassed = now()->diffInSeconds($sentAt);
            $remaining = max(0,  (int)$cooldown - (- (int)$secondsPassed));

            if ($remaining > 0) {
                return response()->json([
                    'remind' => (int)$remaining, // مقدار زمان باقی‌مانده
                    'message' => "Please try again after {$remaining} seconds."
                ]);
            }
        }




        $code = random_int(100000, 999999);

        Cache::put($cacheKey, [
            'code' => $code,
            'tries' => 0,
            'sent_at' => now()->toDateTimeString(), // <- به جای object
        ], 120);


        $responseMessages = [];

        try {
            if (!empty($data['email'])) {
                Mail::to($data['email'])->send(new OtpMail(['code' => $code]));
                $responseMessages['email'] = 'The verification code has been sent to your email.';
                $responseMessages['remind'] = 120;
            }
        } catch (Throwable $e) {
            Log::error('OTP Mail Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send the verification code. Please try again.',
            ], 500);
        }

        return response()->json($responseMessages, 200);
    }



}
