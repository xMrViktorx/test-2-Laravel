<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('users')->middleware(['auth', 'permission:user-management'])->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/update/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('users.delete');
});

Route::prefix('permissions')->middleware(['auth', 'permission:user-management'])->group(function () {
    Route::get('/', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/store', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/edit/{id}', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('/update/{id}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/delete/{id}', [PermissionController::class, 'destroy'])->name('permissions.delete');
    Route::post('/assign', [PermissionController::class, 'assignPermission'])->name('permissions.assign');
});
