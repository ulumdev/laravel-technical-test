<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// Landing Page (for public access)
Route::get('/', function () {
    return Inertia::render('Landing/Index');
})->name('landing');

// Authentication Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard for all roles
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD Role (all roles)
    Route::resource('/roles', RoleController::class)->only([
        'index',
        'create',
        'store',
        'edit',
        'update',
        'destroy',
        'show'
    ]);


    // CRUD User (all roles)
    Route::resource('/users', UserController::class)->only([
        'index',
        'create',
        'store',
        'edit',
        'update',
        'destroy',
        'show'
    ]);

    // CRUD User (only for admin)
    // Route::middleware(['role:Administrator'])->group(function () {
    //     Route::resource('/users', UserController::class);
    // });
});

require __DIR__ . '/auth.php';
