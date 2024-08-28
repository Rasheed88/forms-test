<?php

use Illuminate\Support\Facades\Route;


Route::get('/form/{id}', [FormController::class, 'displayForm']);

Route::get('/', function () {
    return view('welcome');
});
