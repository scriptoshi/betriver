<?php

use App\Http\Controllers\InstallerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InstallerController::class, 'welcome'])->name('welcome');
Route::get('/requirements', [InstallerController::class, 'requirements'])->name('requirements');
Route::get('/permissions', [InstallerController::class, 'permissions'])->name('permissions');
Route::get('/database', [InstallerController::class, 'databaseForm'])->name('database');
Route::post('/database', [InstallerController::class, 'saveDatabase'])->name('database.save');
Route::get('/migration', [InstallerController::class, 'migration'])->name('migration');
Route::post('/migrate', [InstallerController::class, 'migrate'])->name('migrate');
Route::get('/finished', [InstallerController::class, 'finish'])->name('finish');
