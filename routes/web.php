<?php

use App\Http\Controllers\PrizesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('test', 'test');

Route::get('/', function () {
    return redirect()->route('prizes.index');
});

Route::resource('prizes', PrizesController::class);

Route::post('/simulate', [PrizesController::class, 'simulate'])->name('simulate');
Route::post('/reset', [PrizesController::class, 'reset'])->name('reset');
