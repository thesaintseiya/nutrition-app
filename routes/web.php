<?php

use App\Http\Controllers\Auth\FavouriteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GenerateMealsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MealController;
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

Route::get('/', fn () => to_route('home'));
Route::get('/home', HomeController::class)->name('home');
Route::post('/home', [GenerateMealsController::class, 'generate'])->name('generate');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::post('/favourite', [FavouriteController::class, 'store'])->name('favourite');
    Route::delete('/favourite/{favourite}', [FavouriteController::class, 'destroy'])->name('favourite');
});

Route::middleware('guest')->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'create')->name('login');
        Route::post('/login', 'store')->name('login');
    });

    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'create')->name('register');
        Route::post('/register', 'store')->name('register');
    });
});
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

Route::controller(IngredientController::class)->group(function () {
    Route::get('/ingredients', 'index')->name('ingredients.index');
    Route::get('/ingredients/{ingredient}', 'show')->name('ingredients.show');
    Route::post('/ingredients', 'store')->name('ingredients.store')->middleware('auth');
    Route::post('/ingredients/{ingredient}', 'update')->name('ingredients.update')->middleware('auth');
});

Route::controller(MealController::class)->group(function () {
    Route::get('/meals', 'index')->name('meals.index');
    Route::get('/meals/{meal}', 'show')->name('meals.show');
    Route::post('/meals', 'store')->name('meals.store')->middleware('auth');
    Route::post('/meals/{meal}', 'update')->name('meals.update')->middleware('auth');
});
