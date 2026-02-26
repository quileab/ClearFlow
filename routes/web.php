<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/login', 'login')->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    })->name('logout');

    Volt::route('/', 'dashboard')->name('home');
    Volt::route('/profile', 'profile')->name('profile');
    Volt::route('/categories', 'categories.index')->name('categories');
    Volt::route('/movements', 'movements.index')->name('movements');
    Volt::route('/reports', 'reports.index')->name('reports');
    Volt::route('/users', 'users.index')->name('users');
});
