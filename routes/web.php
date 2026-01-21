<?php

use App\Http\Controllers\InvitationsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/u/{code}', [ShortUrlController::class, 'redirect'])->name('urls.redirect');

Route::get('/invitations/accept/{token}', [InvitationsController::class, 'accept'])->name('invitations.accept');
Route::post('/invitations/accept/{token}', [InvitationsController::class, 'process'])->name('invitations.process');

Route::get('/dashboard', function() {
    $user = Auth::user();
    if ($user->isSuperAdmin()) {
        return redirect()->route('superadmin.dashboard');
    }
    return redirect()->route('urls.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/invitations', [InvitationsController::class, 'index'])->name('invitations.index');
    Route::post('/invitations', [InvitationsController::class, 'store'])->name('invitations.store');

    Route::get('urls', [ShortUrlController::class, 'index'])->name('urls.index');
    Route::post('urls', [ShortUrlController::class, 'store'])->name('urls.store');
    Route::get('urls/export', [ShortUrlController::class, 'export'])->name('urls.export');
    
    Route::get('/super-admin/dashboard', [ShortUrlController::class, 'index'])->name('superadmin.dashboard');
});

require __DIR__.'/auth.php';