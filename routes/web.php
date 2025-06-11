<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CountryController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/import-country-data', [CountryController::class, 'importData']);
