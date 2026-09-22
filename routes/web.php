<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// initial route for the welcome page
Route::get('/', function () {
    return view('welcome');
});

// route for the about page
Route::get('/about', function () {
    return view('about');
});

Route::resource('posts', PostController::class);
