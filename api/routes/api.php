<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\ReviewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [UserController::class, 'login'])->name('login');

// Web Info
Route::get('fetchAllWebInformation', [InformationController::class, 'fetchAllWebInformation'])->name('fetchAllWebInformation');

// Categories
Route::get('fetchAllCategories', [CategoryController::class, 'fetchAllCategories'])->name('fetchAllCategories');

// Images
Route::get('fetchAllImages', [ImageController::class, 'fetchImages'])->name('fetchAllImages');
Route::get('getImagesByCategory/{id}', [ImageController::class, 'getImagesByCategory'])->name('getImagesByCategory');

// Reviews
Route::post('reviewRegister', [ReviewController::class,'reviewRegister'])->name('reviewRegister');
Route::get('fetchReviews', [ReviewController::class,'fetchReviews'])->name('fetchReviews');


//Prospects 
Route::post('prospectRegister', [ProspectController::class,'prospectRegister'])->name('prospectRegister');

Route::post('createUser', [UserController::class, 'userRegister'])->name('createUser');


Route::group(['middleware' => 'ApiAuthMiddleware'], function () {
    // Users
    Route::put('userUpdate/{id}', [UserController::class, 'userUpdate'])->name('userUpdate');
    Route::delete('userDelete/{id}', [UserController::class, 'userDelete'])->name('userDelete');
    Route::get('fetchUser/{id}', [UserController::class, 'fetchUser'])->name('fetchUser');
    Route::get('fetchUsers', [UserController::class, 'fetchUsers'])->name('fetchUsers');

    //Web Information
    Route::post('registerWebInformation', [InformationController::class, 'registerWebInformation'])->name('registerWebInformation');
    Route::get('fetchWebInformation/{id}', [InformationController::class, 'fetchWebInformation'])->name('fetchWebInformation');
    Route::put('updateWebInformation/{id}', [InformationController::class, 'updateWebInformation'])->name('updateWebInformation');
    Route::delete('deleteWebInformation/{id}', [InformationController::class, 'deleteWebInformation'])->name('deleteWebInformation');

    //Categories
    Route::post('registerCategory', [CategoryController::class, 'registerCategory'])->name('registerCategory');
    Route::put('updateCategory/{id}', [CategoryController::class, 'updateCategory'])->name('updateCategory');
    Route::delete('deleteCategory/{id}', [CategoryController::class, 'deleteCategory'])->name('deleteCategory');
    Route::get('fetchCategoryById/{id}', [CategoryController::class, 'fetchCategoryById'])->name('fetchCategoryById');

    //Images
    Route::post('uploadImage', [ImageController::class, 'uploadImage'])->name('uploadImage');
    Route::post('updateImage/{id}', [ImageController::class, 'updateImage'])->name('uploadImage');
    Route::delete('deleteImage/{id}', [ImageController::class, 'deleteImage'])->name('deleteImage');

    //Prospects
    Route::get('getProspects', [ProspectController::class, 'getProspects'])->name('getProspects');

    // Reviews
    Route::put('reviewUpdate/{id}', [ReviewController::class,'reviewUpdate'])->name('reviewUpdate');
    Route::delete('reviewDelete/{id}', [ReviewController::class,'reviewDelete'])->name('reviewDelete');
});
Route::post('getImageById/{id}', [ImageController::class, 'getImageById'])->name('getImageById');
Route::post('getImage/{name}', [ImageController::class, 'getImage'])->name('getImage');


