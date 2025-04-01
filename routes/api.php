<?php

use App\Http\Controllers\AnimalBreedController;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\AnimalSpecieController;
use App\Http\Controllers\AnimalTypeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchTypeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CountryController;

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

//Auth
Route::post('login'  , [AuthController::class, 'login']);
Route::post('register' , [AuthController::class, 'register']);
Route::post('entity-register' , [AuthController::class, 'entity_register']);
Route::post('verify-account' , [AuthController::class, 'verify_account']);
Route::post('logout', [AuthController::class, 'logout']);

Route::put('user', [AuthController::class, 'edit_profile']);
Route::get('user', [AuthController::class, 'get_profile']);
Route::delete('user/delete_account', [AuthController::class, 'delete_user']);

//branch_types
Route::get('branch-types'  , [BranchTypeController::class, 'index']);
Route::get('branch-types/{branch_type}'  , [BranchTypeController::class, 'show']);

//animals
Route::get('animals', [AnimalController::class, 'index']);

//countries
Route::get('countries'  , [CountryController::class, 'index']);
Route::get('countries/{country}'  , [CountryController::class, 'show']);

//categories
Route::get('categories'  , [CategoryController::class, 'index']);
Route::get('categories/{category}'  , [CategoryController::class, 'show']);

//colors
Route::get('colors'  , [ColorController::class, 'index']);
Route::get('colors/{color}'  , [ColorController::class, 'show']);

//animal-types
Route::get('animal-types'  , [AnimalTypeController::class, 'index']);
Route::get('animal-types/{animal_type}'  , [AnimalTypeController::class, 'show']);

//animal-species
Route::get('animal-species'  , [AnimalSpecieController::class, 'index']);
Route::get('animal-species/{animal_specie}'  , [AnimalSpecieController::class, 'show']);

//animal-breeds
Route::get('animal-breeds'  , [AnimalBreedController::class, 'index']);
Route::get('animal-breeds/{animal_breed}'  , [AnimalBreedController::class, 'show']);