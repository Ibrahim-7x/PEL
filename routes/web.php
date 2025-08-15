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
    Route::get('/home-agent', [AgentController::class, 'index'])->name('agent.index');
    Route::post('/home-agent', [AgentController::class, 'store'])->name('agent.store');
});

Route::middleware(['auth', 'role:Agent'])->group(function () {

    Route::get('/home-agent', [App\Http\Controllers\AgentController::class, 'index'])->name('agent.index');

    Route::post('/home-agent', [App\Http\Controllers\AgentController::class, 'store'])->name('agent.store');

});

Route::get('/home-agent/ticket/{ticket_no}', [AgentController::class, 'showTicket'])
    ->middleware(['auth', 'role:Agent'])
    ->name('agent.ticket');

// Post feedback for that ticket
Route::post('/home-agent/ticket/{ticket_no}/feedback', [AgentController::class, 'storeFeedback'])
    ->middleware(['auth', 'role:Agent'])
    ->name('agent.feedback.store');


Route::middleware(['auth', 'role:Management'])->group(function () {
    Route::get('/home-management', [App\Http\Controllers\ManagementController::class, 'index'])->name('management');
});

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');