<?php

use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - DEL Lab Equipment & Asset Tracking System (DEL-LEATS)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('equipments.index');
});

// Equipment Routes
Route::get('/equipments', [EquipmentController::class, 'index'])->name('equipments.index');
Route::get('/equipments/create', [EquipmentController::class, 'create'])->name('equipments.create');
Route::post('/equipments', [EquipmentController::class, 'store'])->name('equipments.store');
Route::get('/equipments/{id}', [EquipmentController::class, 'show'])->name('equipments.show');

// Loan Routes
Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
