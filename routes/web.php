<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', fn () => Inertia::render('Home'))->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('membros')->group(function () {
        Route::get('/', [MemberController::class, 'index'])->name('members.index');
        Route::get('/patrimonial-members', [MemberController::class, 'getAllPatrimonialMembers'])->name('members.getPatrimonialMembers');
        Route::post('/', [MemberController::class, 'store'])->name('members.store');
        Route::patch('/{member}', [MemberController::class, 'update'])->name('members.update');
        Route::patch('/{id}/disable', [MemberController::class, 'disable'])->name('members.disable');
        Route::patch('/{id}/enable', [MemberController::class, 'enable'])->name('members.enable');
        Route::post('/import', [MemberController::class, 'import'])->name('members.import');
    });

    Route::prefix('mensalidades')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::post('/register-payment', [SubscriptionController::class, 'registerPayment'])->name('subscriptions.registerPayment');
        Route::post('/exempt-month', [SubscriptionController::class, 'exemptMonth'])->name('subscriptions.exempt');
    });
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
