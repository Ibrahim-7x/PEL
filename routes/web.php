<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\ExportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Auth::routes();

Route::get('/profile', function () {
    return view('profile');
})->middleware('auth')->name('profile');

// Agent routes
Route::middleware(['auth', 'role:Agent'])->group(function () {
    Route::get('/home-agent', [AgentController::class, 'index'])->name('agent.index');
    Route::post('/home-agent', [AgentController::class, 'store'])->name('agent.store');
    Route::get('/home-agent/ticket/{ticket_no}', [AgentController::class, 'showTicket'])->name('agent.ticket');
    Route::post('/home-agent/ticket/{ticket_no}/feedback', [AgentController::class, 'storeFeedback'])->name('agent.feedback.store');
    Route::post('/home-agent/search-ticket', [App\Http\Controllers\AgentController::class, 'searchTicket'])->name('agent.ticket.search');
    Route::get('/home-agent/ticket/{ticket_no}/feedbacks', [AgentController::class, 'getFeedbacks'])->name('agent.feedback.list');
    Route::post('/agent/{ticket_no}/happy-call', [AgentController::class, 'saveHappyCallStatus'])->name('agent.happy-call.save');});

// Management routes
Route::middleware(['auth', 'role:Management'])->group(function () {
    Route::get('/home-management', [ManagementController::class, 'index'])->name('management.index');
    Route::get('/home-management/ticket/{ticket_no}', [ManagementController::class, 'showTicket'])->name('management.ticket');
    Route::post('/home-management/ticket/{ticket_no}/feedback', [ManagementController::class, 'storeFeedback'])->name('management.feedback.store');
    Route::post('/home-management/search-ticket', [App\Http\Controllers\ManagementController::class, 'searchTicket'])->name('management.ticket.search');
    Route::get('/home-management/ticket/{ticket_no}/feedbacks', [ManagementController::class, 'getFeedbacks'])->name('management.feedback.list');
});

// Logout
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');


// Export routes



Route::middleware(['auth', 'role:Management'])->group(function () {
    Route::get('/export/initial-customer', [ExportController::class, 'initialCustomerPage'])->name('export.initial_customer');
    Route::post('/export/initial-customer/download', [ExportController::class, 'exportInitialCustomer'])->name('export.initial_customer.download');

    Route::get('/export/happy-call', [ExportController::class, 'happyCallPage'])->name('export.happy_call');
    Route::post('/export/happy-call/download', [ExportController::class, 'exportHappyCall'])->name('export.happy_call.download');

    Route::get('/export/feedback', [ExportController::class, 'feedbackPage'])->name('export.feedback');
    Route::post('/export/feedback/download', [ExportController::class, 'exportFeedback'])->name('export.feedback.download');
});




