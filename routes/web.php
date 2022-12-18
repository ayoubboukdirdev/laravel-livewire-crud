<?php

// use App\Http\Controllers\PostController;
use App\Http\Livewire\Posts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


route::group(['middleware' => 'auth'],function(){
    // Route::resource('/posts', PostController::class);
    try {
        Route::get('posts', Posts::class);
    } catch (\Throwable $th) {
        
    }
});






