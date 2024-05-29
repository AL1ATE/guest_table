<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/guests', [GuestController::class, 'index']);
Route::post('/guests', [GuestController::class, 'store']);
Route::put('/guests/{guest}', [GuestController::class, 'update']);
Route::delete('/guests/{guest}', [GuestController::class, 'destroy']);

