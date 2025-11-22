<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Controllers
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\TaskController;


// ==========================
// STATIC PAGES (PUBLIC)
// ==========================
$staticPages = [
    '/',
    '/about',
    '/features',
    '/timer',
];

// match / → home.jsx, /about → about.jsx, etc
$items = array_map(function ($page) {
    return ($name = ltrim($page, '/')) === '' ? 'home' : $name;
}, $staticPages);

$associate = array_map(function ($page, $file) {
    return ['page' => $page, 'file' => $file];
}, $staticPages, $items);

foreach ($associate as $item) {
    Route::get($item['page'], fn () => Inertia::render($item['file']));
}


// ==========================
// AUTH ROUTES
// ==========================

// Register Page
Route::get('/register', fn () => Inertia::render('register'))->name('register');

// Register Action (generates OTP)
Route::post('/register', [RegisterController::class, 'store'])
    ->middleware('throttle:5,1');

// Login Page
Route::get('/login', fn () => Inertia::render('login'))->name('login');

// Login Action
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('throttle:5,1');

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');


// ==========================
// OTP ROUTES
// ==========================

// Show OTP Page
Route::get('/verify-otp', [OtpController::class, 'show'])->name('otp.show');

// Verify OTP
Route::post('/verify-otp', [OtpController::class, 'verify'])->name('otp.verify');

// Resend OTP
Route::post('/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');


// ==========================
// FORGOT PASSWORD
// ==========================
Route::get('/forgot-password', fn () => Inertia::render('forgotpassword'))
    ->name('password.request');


// ==========================
// PROTECTED ROUTES (AUTH REQUIRED)
// ==========================
Route::middleware(['auth'])->group(function () {

    Route::get('/todolist', [TaskController::class, 'index'])->name('todolist');

    Route::get('/schedule', [TaskController::class, 'schedule'])->name('schedule');

    // Timer page (if separate from static)
    Route::get('/timer', fn () => Inertia::render('timer'))->name('timer');

    // CRUD Task API
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
});


// ==========================
// DEBUG ONLY
// ==========================
Route::get('/whoami', function () {
    return [
        'auth_id' => auth()->id(),
        'session' => session()->all(),
    ];
});

Route::get('/test-mail', function () {
    Mail::raw("Testing Gmail SMTP!", function ($msg) {
        $msg->to("ichirodextherrewah@gmail.com")->subject("Gmail SMTP Working!");
    });
    return "sent";
});
