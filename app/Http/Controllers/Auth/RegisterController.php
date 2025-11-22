<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    // TTL OTP (menit) – samakan dengan OtpController
    private int $otpTtlMinutes = 10;

    public function store(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Buat user baru
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // JANGAN auto-login di sini
        // Auth::login($user);

        // Simpan email di session untuk konteks OTP
        session(['otp_email' => $user->email]);

        // ==== Generate & simpan OTP di cache ====
        $email = $user->email;

        $otp       = random_int(100000, 999999);
        $hashedOtp = Hash::make($otp);

        $otpKey      = "otp:register:{$email}";
        $attemptsKey = "otp:register:{$email}:attempts";
        $resendKey   = "otp:register:{$email}:resend_count";

        Cache::put($otpKey, $hashedOtp, now()->addMinutes($this->otpTtlMinutes));
        Cache::put($attemptsKey, 0, now()->addMinutes($this->otpTtlMinutes));
        Cache::put($resendKey, 0, now()->addMinutes($this->otpTtlMinutes));

        // Kirim OTP via email
        Mail::raw("Your Tracklin verification code is: {$otp}", function ($message) use ($email) {
            $message->to($email)
                    ->subject('Your Tracklin OTP Code');
        });

        // Redirect ke halaman OTP
        return redirect()
            ->route('otp.show')
            ->with('success', 'We have sent a verification code to your email.');
    }
}
