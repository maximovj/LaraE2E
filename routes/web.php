<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use App\Models\UserProfile;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

/* Ruta Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return 'Página de admin';
    });
});
*/

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('/employees', EmployeeController::class)
    ->middleware(['auth', 'verified', 'role:company-admin|office-manager']);

Route::get('/employees/{employee:employee_number}/user',
    [EmployeeController::class, 'user'])
    ->name('employees.user')
    ->middleware(['auth', 'verified', 'role:company-admin|office-manager']);

Route::get('/employee/{employee:employee_number}/user-profile',
    [EmployeeController::class, 'user_profile'])
    ->name('employees.user_profile')
    ->middleware(['auth', 'verified', 'role:company-admin|office-manager']);

Route::resource('/users', UserController::class)
    ->middleware(['auth', 'verified']);

Route::post('/users/validate',
    [UserController::class, 'validateForm'])
    ->name('users.validate.store')
    ->middleware(['auth', 'verified']);

Route::post('/users/{user}/validate',
    [UserController::class, 'validateForm'])
    ->name('users.validate.update')
    ->middleware(['auth', 'verified']);

Route::resource('user-profiles', UserProfileController::class)
    ->middleware(['auth', 'verified']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
