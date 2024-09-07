<?php

use App\Http\Controllers\PropertyController;
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
Route::middleware('auth')->group(function () {

    Route::get('/home', [function () {
        return redirect()->route('home');
    }]);
    Route::get('/', [PropertyController::class, 'index'])->name('home');
    Route::get('/create', [PropertyController::class, 'viewCreateForm'])->name('properties.create');
    Route::post('/create', [PropertyController::class, 'store'])->name('properties.store');

    Route::get('/{property}', [PropertyController::class, 'show'])->name('properties.show');


    Route::get('/edit/{property}', [PropertyController::class, 'edit'])->name('properties.edit');
    Route::put('/update/{property}', [PropertyController::class, 'update'])->name('properties.update');

    Route::delete('/delete/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');
});
