<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // Ensure the application uses HTTPS
    if (!app()->environment('local') && !request()->isSecure()) {
        return redirect()->secure(request()->getRequestUri());
    }

    // Check if the user is authenticated
    if (auth()->check()) {
        // User is authenticated, return a 200 response
        return response('Welcome to the admin dashboard', 200)
            ->header('Content-Security-Policy', "default-src 'self'; script-src 'self'; style-src 'self';")
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('X-Frame-Options', 'DENY')
            ->header('X-XSS-Protection', '1; mode=block');
    }

    // Rate limiting to prevent brute force attacks
    $ip = request()->ip();
    if (RateLimiter::tooManyAttempts($ip, 5)) {
        return response('Too many login attempts. Please try again later.', 429);
    }
    RateLimiter::hit($ip);

    // User is not authenticated, redirect to the admin login page
    $url = env('APP_URL', 'http://127.0.0.1:8000') . '/admin/login';

    return redirect($url)->withHeaders([
        'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self';",
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'DENY',
        'X-XSS-Protection' => '1; mode=block',
    ]);
});

// CSRF protection is enabled by default in Laravel for all POST requests
// Ensure secure cookies
config(['session.secure' => true, 'session.http_only' => true, 'session.same_site' => 'strict']);