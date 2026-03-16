<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/', [PostController::class,'index']);

Route::get('/posts/create',[PostController::class,'create'])->name('posts.create');

Route::post('/posts/store',[PostController::class,'store'])->name('posts.store');

Route::get('/search',[PostController::class,'search'])->name('posts.search');

// Route::get('/', function () {
//     return view('welcome');
// });
