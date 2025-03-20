<?php

use App\Http\Controllers\User\AnimalController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/animals/{animal}/generate-token', [AnimalController::class, 'generateTransferToken']);
Route::post('/animals/accept-transfer', [AnimalController::class, 'acceptTransfer']);
