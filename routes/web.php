<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\DataImportController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ImportedDataController;

// Authentication routes
Auth::routes();

// Home route
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// User management routes
Route::prefix('users')->middleware(['auth', 'permission:user-management'])->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/update/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('users.delete');
});

// Permission management routes
Route::prefix('permissions')->middleware(['auth', 'permission:user-management'])->group(function () {
    Route::get('/', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/store', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/edit/{id}', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('/update/{id}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/delete/{id}', [PermissionController::class, 'destroy'])->name('permissions.delete');
    Route::post('/assign', [PermissionController::class, 'assignPermission'])->name('permissions.assign');
});

// Data import routes
Route::prefix('data-import')->middleware(['auth', 'permission:data-import'])->group(function () {
    Route::get('/', [DataImportController::class, 'index'])->name('data-import.index');
    Route::post('/import', [DataImportController::class, 'validateImport'])->name('data-import.import');
});

// Import history routes
Route::prefix('imported-data')->middleware(['auth'])->group(function () {
    Route::get('/{file}', [ImportedDataController::class, 'index'])->name('imported-data.index');
    Route::get('/{file}/export', [ImportedDataController::class, 'export'])->name('imported-data.export');
    Route::delete('/delete/{id}', [ImportedDataController::class, 'destroy'])->name('imported-data.delete');
});

// Imported files list routes
Route::prefix('imports')->middleware(['auth'])->group(function () {
    Route::get('/', [ImportController::class, 'index'])->name('imports.index');
    Route::delete('/delete/{id}', [ImportController::class, 'destroy'])->name('imports.delete');
});