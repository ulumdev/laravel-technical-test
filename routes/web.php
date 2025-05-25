<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskController;
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
    Route::resource('/roles', RoleController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy', 'show']);


    // CRUD User (all roles)
    Route::resource('/users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy', 'show']);

    // CRUD User (only for admin)
    // Route::middleware(['role:Administrator'])->group(function () {
    //     Route::resource('/users', UserController::class);
    // });

    // CRUD Project (all roles)
    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/restore', [ProjectController::class, 'restore'])->name('projects.restore');
    Route::delete('projects/{project}/force-delete', [ProjectController::class, 'forceDelete'])->name('projects.forceDelete');

    // Route::get('projects/export', [ProjectController::class, 'export'])->name('projects.export');
    // Route::get('projects/export', [ProjectController::class, 'exportExcel'])->name('projects.export');
    // Route::post('projects/import', [ProjectController::class, 'importExcel'])->name('projects.import');

    // CRUD Task (all roles)
    Route::resource('tasks', TaskController::class);
    // Tambahkan kedua route ini!
    // Route::put('attachments/{attachment}', [AttachmentController::class, 'update'])->name('attachments.update');
    Route::post('attachments/{attachment}', [AttachmentController::class, 'update']);
    Route::post('tasks/{task}/restore', [TaskController::class, 'restore'])->name('tasks.restore');
    Route::delete('tasks/{task}/force-delete', [TaskController::class, 'forceDelete'])->name('tasks.forceDelete');

    // CRUD Attachment (all roles)
    // Route::resource('attachments', AttachmentController::class);
    // Route::post('attachments/{attachment}/restore', [AttachmentController::class, 'restore'])->name('attachments.restore');
    // Route::delete('attachments/{attachment}/force-delete', [AttachmentController::class, 'forceDelete'])->name('attachments.forceDelete');


    // Route for Export and Import Excel
    Route::get('{entity}/export', [\App\Http\Controllers\ExcelController::class, 'export']);

    Route::get('export-jobs', [\App\Http\Controllers\ExcelController::class, 'exportJobs']);
    Route::get('download-export', [\App\Http\Controllers\ExcelController::class, 'downloadExport']);

    Route::post('{entity}/import', [\App\Http\Controllers\ExcelController::class, 'import']);
    Route::get('import-jobs', [\App\Http\Controllers\ExcelController::class, 'importJobs']);
    // Route::get('download-export', [\App\Http\Controllers\ExcelController::class, 'download']);
});

require __DIR__ . '/auth.php';
