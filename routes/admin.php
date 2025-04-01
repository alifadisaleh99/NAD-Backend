<?php

use App\Http\Controllers\Admin\AnimalBreedController;
use App\Http\Controllers\Admin\AnimalController;
use App\Http\Controllers\Admin\AnimalTypeController;
use App\Http\Controllers\Admin\AnimalSpecieController;
use App\Http\Controllers\Admin\AnimalStatusController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\BranchTypeController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\EntityController;
use App\Http\Controllers\Admin\LogoController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PetMarkController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\TagTypeController;
use App\Http\Controllers\Admin\UserController;
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

// roles and permissions
Route::get('/permissions', [PermissionController::class, 'get_all_permissions']);
Route::get('/permissions/me', [PermissionController::class, 'my_permissions']);
Route::get('/roles/{role}/permissions', [PermissionController::class, 'get_permissions']);
Route::post('/roles/{role}/permissions', [PermissionController::class, 'set_permissions']);
Route::apiResource('roles', RoleController::class);

// users
Route::apiResource('users' , UserController::class);

// entities
Route::apiResource('entities', EntityController::class);

// branches
Route::apiResource('branches', BranchController::class);

// animal resources
Route::apiResource('categories', CategoryController::class);
Route::apiResource('animal-types', AnimalTypeController::class);
Route::apiResource('animal-species', AnimalSpecieController::class)->parameters(['animal-species' => 'animal_specie']);
Route::apiResource('animal-breeds', AnimalBreedController::class);
Route::apiResource('colors', ColorController::class);
Route::apiResource('pet-marks', PetMarkController::class);

Route::name('admin.')->group(function () {
    Route::apiResource('animals',AnimalController::class);
});


// animal
Route::apiResource('animals', AnimalController::class);
Route::apiResource('animal-statuses', AnimalStatusController::class);
Route::get('animals/{animal}/ownership-records', [AnimalController::class, 'ownershipRecords']); 
Route::post('animals/{animal}/generate-token', [AnimalController::class, 'generateTransferToken']);

//plan
Route::apiResource('plans', PlanController::class);

//tag_types
Route::apiResource('tag-types', TagTypeController::class);

//tags
Route::put('tags', [TagController::class, 'update']);  


//Statistics
Route::get('statistics/count-animals', [StatisticsController::class,'getAnimalsCount']);
Route::get('statistics/plan-earnings', [StatisticsController::class,'getPlanEarnings']);
Route::get('statistics/subscriptions', [StatisticsController::class,'getsubscriptions']);

//logo
Route::get('logo', [LogoController::class, 'show']);
Route::put('logo', [LogoController::class, 'update']);

//branch_types
Route::apiResource('branch-types', BranchTypeController::class);
