<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\ManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Auth::routes();

Route::get('/contact', function () {
    return view('contact');
})->middleware('auth')->name('contact');

Route::get('/profile', function () {
    return view('profile');
})->middleware('auth')->name('profile');

Route::get('/about', function () {
    return view('about');
})->middleware('auth')->name('about');

Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');

Route::middleware(['auth', 'role:Agent'])->group(function () {
    Route::match(['get', 'post'], '/home-agent', [App\Http\Controllers\AgentController::class, 'index', 'store'])
        ->name('agent');
});

Route::middleware(['auth', 'role:Agent'])->group(function () {
    // For displaying the form
    Route::get('/home-agent', [App\Http\Controllers\AgentController::class, 'index'])->name('agent.index');

    // For saving form data
    Route::post('/home-agent', [App\Http\Controllers\AgentController::class, 'store'])->name('agent.store');
});


Route::middleware(['auth', 'role:Management'])->group(function () {
    Route::get('/home-management', [App\Http\Controllers\ManagementController::class, 'index'])->name('management');
});

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');