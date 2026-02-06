<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CirculationController;
use App\Http\Controllers\FineController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\GuestController;

// Guest/Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');
Route::get('/catalog', [GuestController::class, 'catalog'])->name('guest.catalog');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management (Admin only)
    Route::resource('users', UserController::class);

    // Student Management
    Route::resource('students', StudentController::class);

    // Book Catalog
    Route::resource('books', BookController::class);

    // Authors
    Route::resource('authors', AuthorController::class);

    // Publishers
    Route::resource('publishers', PublisherController::class);

    // Categories
    Route::resource('categories', CategoryController::class);

    // Circulation
    Route::get('/circulation/issue', [CirculationController::class, 'issueForm'])->name('circulation.issue');
    Route::post('/circulation/issue', [CirculationController::class, 'issueBook'])->name('circulation.issue.store');
    Route::get('/circulation/return', [CirculationController::class, 'returnForm'])->name('circulation.return');
    Route::post('/circulation/return', [CirculationController::class, 'returnBook'])->name('circulation.return.store');
    Route::get('/circulation/history', [CirculationController::class, 'history'])->name('circulation.history');

    // Fine Management
    Route::get('/fines', [FineController::class, 'index'])->name('fines.index');
    Route::post('/fines/{fine}/collect', [FineController::class, 'collect'])->name('fines.collect');
    Route::post('/fines/{fine}/waive', [FineController::class, 'waive'])->name('fines.waive');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

    // Settings (Admin only)
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');

    // Backup & Restore (Admin only)
    Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
    Route::post('/backup', [BackupController::class, 'create'])->name('backup.create');
    Route::post('/backup/restore', [BackupController::class, 'restore'])->name('backup.restore');

    // Audit Logs (Admin only)
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // AJAX endpoints for dynamic searches
    Route::get('/api/students/search', [StudentController::class, 'search'])->name('api.students.search');
    Route::get('/api/books/search', [BookController::class, 'search'])->name('api.books.search');
    Route::get('/api/sections/{gradeLevel}', [StudentController::class, 'getSections'])->name('api.sections');
});
