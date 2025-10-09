<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Auth::routes();

Route::post('/fetch-coms-data', [App\Http\Controllers\AgentController::class, 'fetchComsData'])->middleware('auth')->name('fetch.coms');
Route::post('/check-complaint-ticket', [App\Http\Controllers\AgentController::class, 'checkComplaintTicket'])->middleware('auth')->name('check.complaint.ticket');
Route::post('/fetch-ticket-info', [App\Http\Controllers\AgentController::class, 'fetchTicketInfo'])->middleware('auth')->name('fetch.ticket.info');
Route::get('/generate-ticket-number', [App\Http\Controllers\AgentController::class, 'getTicketNumber'])->middleware('auth')->name('generate.ticket.number');

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/welcome', function () {
    return view('welcome');
})->middleware('auth')->name('welcome');

Route::get('/profile', [ProfileController::class, 'index'])->middleware('auth')->name('profile');
Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->middleware('auth')->name('profile.update-password');

// Heartbeat route for session management (lightweight, no content)
Route::match(['get', 'post'], '/heartbeat', function() {
    return response()->noContent(); // 204 No Content response
})->middleware('auth')->name('heartbeat');

// Agent routes
Route::middleware(['auth', 'role:Agent'])->group(function () {
    Route::get('/home-agent', [AgentController::class, 'index'])->name('agent.index');
    Route::post('/home-agent', [AgentController::class, 'store'])->name('agent.store');
    Route::get('/t-agent', [AgentController::class, 'tIndex'])->name('t_agent.index');
    Route::get('/home-agent/ticket/{ticket_no}', [AgentController::class, 'showTicket'])->name('agent.ticket');
    Route::get('/t-agent/ticket/{ticket_no}', [AgentController::class, 'showTicketT'])->name('t_agent.ticket');
    Route::post('/home-agent/ticket/{ticket_no}/feedback', [AgentController::class, 'storeFeedback'])->name('agent.feedback.store');
    Route::post('/home-agent/search-ticket', [AgentController::class, 'searchTicket'])->name('agent.ticket.search');
    Route::get('/home-agent/ticket/{ticket_no}/feedbacks', [AgentController::class, 'getFeedbacks'])->name('agent.feedback.list');
    Route::post('/agent/{ticket_no}/happy-call', [AgentController::class, 'saveHappyCallStatus'])->name('agent.happy-call.save');
    // Route::post('/home-agent/fetch-coms', [AgentController::class, 'fetchComsData'])->name('agent.fetch.coms');
    // Route::post('/fetch-coms-data', [AgentController::class, 'fetchComsData'])->name('agent.fetch.coms');
});

// Management routes
Route::middleware(['auth', 'role:Management'])->group(function () {
    Route::get('/home-management', [ManagementController::class, 'index'])->name('management.index');
    Route::get('/t-management', [ManagementController::class, 'tIndex'])->name('t_management.index');
    Route::get('/home-management/ticket/{ticket_no}', [ManagementController::class, 'showTicket'])->name('management.ticket');
    Route::post('/home-management/ticket/{ticket_no}/feedback', [ManagementController::class, 'storeFeedback'])->name('management.feedback.store');
    Route::post('/home-management/search-ticket', [ManagementController::class, 'searchTicket'])->name('management.ticket.search');
    Route::get('/home-management/ticket/{ticket_no}/feedbacks', [ManagementController::class, 'getFeedbacks'])->name('management.feedback.list');
    // Route::post('/fetch-coms-data', [ManagementController::class, 'fetchComsData'])->name('agent.fetch.coms');
});

// Export routes
Route::middleware(['auth', 'role:Management'])->group(function () {
    Route::get('/export/initial-customer', [ExportController::class, 'initialCustomerPage'])->name('export.initial_customer');
    Route::post('/export/initial-customer/download', [ExportController::class, 'exportInitialCustomer'])->name('export.initial_customer.download');
    
    Route::get('/export/happy-call', [ExportController::class, 'happyCallPage'])->name('export.happy_call');
    Route::post('/export/happy-call/download', [ExportController::class, 'exportHappyCall'])->name('export.happy_call.download');
    
    Route::get('/export/feedback', [ExportController::class, 'feedbackPage'])->name('export.feedback');
    Route::post('/export/feedback/download', [ExportController::class, 'exportFeedback'])->name('export.feedback.download');
});


// Logout
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');