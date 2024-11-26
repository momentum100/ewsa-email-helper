<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmailController;
use App\Http\Controllers\ProcessEmailsController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\EmailAccountController;
use App\Http\Controllers\UserController;

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
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/emails', [EmailController::class, 'index'])->name('emails.index');
    Route::get('/process-emails', [ProcessEmailsController::class, 'process'])->name('process.emails');
    Route::post('/categorize-emails', [EmailController::class, 'categorize'])->name('categorize.emails');
    
    Route::get('/replies/{id}', [ReplyController::class, 'show'])->name('replies.show');
    Route::post('/send-ai-reply/{id}', [ReplyController::class, 'sendAIReply'])->name('send.ai.reply');

});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('emails.index');
    })->name('dashboard');
});

Route::middleware(['auth:sanctum', 'verified', 'admin'])->group(function () {
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [UserManagementController::class, 'show'])->name('users.show');

    Route::get('/email_accounts/create', function () {
        return view('email_accounts.create');
    })->name('email_accounts.create');

    Route::post('/email_accounts', [EmailAccountController::class, 'store'])->name('email_accounts.store');
    Route::get('/email-accounts', [EmailAccountController::class, 'index'])->name('email_accounts.index');

    Route::post('/emailAccounts', [EmailAccountController::class, 'store'])->name('emailAccounts.store');
    Route::delete('/emailAccounts/{id}/detach', [EmailAccountController::class, 'detach'])->name('emailAccounts.detach');
    Route::post('/emailAccounts/attach', [EmailAccountController::class, 'attach'])->name('emailAccounts.attach');

});

