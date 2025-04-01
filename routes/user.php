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

//animal
Route::get('animals/{animal}/ownership-records', [AnimalController::class, 'ownershipRecords']); 
Route::post('animals/{animal}/generate-token', [AnimalController::class, 'generateTransferToken']);
Route::post('animals/accept-transfer', [AnimalController::class, 'acceptTransfer']);
Route::post('animals/{animal}/report-lost', [AnimalController::class, 'reportLost']);
Route::post('animals/{animal}/mark-as-found', [AnimalController::class, 'markAsFound']);
