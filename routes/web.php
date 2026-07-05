<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| These routes define the public pages, authenticated profile routes,
| blood request workflow, donor registration workflow, and authentication
| routes for the LifeSaver blood bank application.
|
*/

Route::get('/', [HomeController::class, 'index']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/', [ProfileController::class, 'update'])->name('update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
});

Route::post('/find-blood', [HomeController::class, 'handleSearch'])->name('blood.handleSearch');

Route::get('/request-blood', [App\Http\Controllers\BloodRequestController::class, 'showForm'])->name('blood.request.form');
Route::post('/request-blood', [App\Http\Controllers\BloodRequestController::class, 'submitForm'])->name('blood.request.submit');

Route::get('/register/donor', [App\Http\Controllers\DonorRegistrationController::class, 'showRegistrationForm'])->name('donor.register.form');
Route::post('/register/donor', [App\Http\Controllers\DonorRegistrationController::class, 'registerDonor'])->name('donor.register.submit');

require __DIR__ . '/auth.php';
