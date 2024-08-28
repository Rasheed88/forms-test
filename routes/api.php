<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/create-form', [FormController::class, 'create']);
Route::get('/forms', [FormController::class, 'index']);
Route::get('/form/{id}', [FormController::class, 'show']);
Route::post('/form/submit/{id}', [FormController::class, 'submit']);

