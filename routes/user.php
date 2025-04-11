<?php

use App\Http\Controllers\User\AnimalController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\TagController;
use App\Http\Controllers\User\TagTypeController;
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
Route::name('user.')->group(function () {
    Route::apiResource('animals',AnimalController::class);
});

//tag_types
Route::get('tag-types', [TagTypeController::class, 'index']); 
Route::get('tag-types/{tag_type}', [TagTypeController::class, 'show']); 

//tag
Route::put('tags', [TagController::class, 'update']);  

//categories
Route::get('categories', [CategoryController::class, 'index'])->name('user.categories.index');
Route::get('categories/{category}'  , [CategoryController::class, 'show'])->name('user.categories.show');

