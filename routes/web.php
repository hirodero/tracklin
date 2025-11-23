<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Auth\OtpController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Model\User;

$staticPages = [
    '/',
    '/about',
    '/features',
    '/timer'
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


Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:5,1');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');
Route::post('/verify-otp', [OtpController::class, 'verify'])->name('otp.verify');
Route::post('/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');
Route::post('/forgot-password', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email']
    ]);

    $status = Password::sendResetLink($request->only('email'));
    if ($status === Password::RESET_LINK_SENT) {
        return back()->with('status', __($status));
    } 

    return back()->withErrors([
        'email' => __($status),
    ]);
})->middleware('guest')->name('password.email');
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

    if ($status === Password::PASSWORD_RESET) {
        return redirect()->route('login')->with('status', __($status));
    }

    return back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.update');


Route::get('/verify-otp', [OtpController::class, 'show'])->name('otp.show');
Route::get('/register', fn () => Inertia::render('register'))->name("register");
Route::get('/login', fn () => Inertia::render('login'))->name('login');
Route::get('/forgot-password', function () {
    return Inertia::render('forgotpassword');
})->middleware('guest')->name('password.request');
Route::get('/reset-password/{token}', function (Request $request, string $token) {
    return Inertia::render('ResetPassword', [
        'token' => $token,
        'email' => $request->query('email'),
    ]);
})->middleware('guest')->name('password.reset');


Route::middleware(['auth'])->group(function () {

    Route::get('/todolist', [TaskController::class, 'index'])->name('todolist');
    Route::get('/schedule', [TaskController::class, 'schedule'])->name('schedule');
    Route::get('/timer', [TaskController::class, 'index'])->name('timer');

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
