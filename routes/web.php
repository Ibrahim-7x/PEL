<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;

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

Route::get('/', [HomeController::class, 'index'])->name('home')->middleware('auth');
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');