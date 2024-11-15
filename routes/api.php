<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SectionController;

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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_plugin')
])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/sections/{section}', [SectionController::class, 'show']);
    Route::post('/sections/{section}/complete', [SectionController::class, 'complete']);
});

require __DIR__.'/auth.php';
