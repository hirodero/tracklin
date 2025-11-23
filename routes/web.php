<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\OtpController;

use App\Http\Controllers\TaskController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


$staticPages = [
    '/',
    '/about',
    '/features',
];

$items = array_map(function ($page) {
    return ($name = ltrim($page, '/')) === '' ? 'home' : $name;
}, $staticPages);

$associate = array_map(function ($a, $b) {
    return ['page' => $a, 'file' => $b];
}, $staticPages, $items);

foreach ($associate as $item) {
    Route::get($item['page'], fn () => Inertia::render($item['file']));
}

Route::get('/register', fn () => Inertia::render('register'))->name("register");
Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:5,1');

Route::get('/login', fn () => Inertia::render('login'))->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('/verify-otp', [OtpController::class, 'show'])->name('otp.show');
Route::post('/verify-otp', [OtpController::class, 'verify'])->name('otp.verify');

Route::post('/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');


Route::get('/forgot-password', function () {
    return Inertia::render('forgotpassword');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => ['required', 'email']]);

    $status = Password::sendResetLink($request->only('email'));

    return $status === Password::RESET_LINK_SENT
        ? back()->with('status', __($status))
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function (Request $request, string $token) {
    return Inertia::render('ResetPassword', [
        'token' => $token,
        'email' => $request->query('email'),
    ]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token'    => 'required',
        'email'    => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.update');


Route::middleware(['auth'])->group(function () {

    Route::get('/timer', fn () => Inertia::render('timer'))->name('timer');

    Route::get('/todolist', [TaskController::class, 'index'])->name('todolist');
    Route::get('/schedule', [TaskController::class, 'schedule'])->name('schedule');

    Route::post('/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
});


Route::get('/whoami', function () {
    return [
        'auth_id' => auth()->id(),
        'session' => session()->all(),
    ];
});
