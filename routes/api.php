<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\Auth\AuthController;
use App\Http\Controllers\User\AdressController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\ProfileController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::POST('/signin','signin');
    Route::POST('redister','register');
    Route::POST('verifyphone','verifyphone');
});
Route::controller(ProfileController::class)->group(function () {
    Route::get('/edit/profile','edit');
    Route::get('/edit/profile/phone','editphone');
    Route::post('update/profile','update');
    Route::post('profile/updatepassword','updatepassword');
    Route::post('profile/updatepassword','updatepassword');
    Route::post('profile/updatephone','updatephone');
    Route::post('verified/phone','verifyphone');
});
Route::controller(OrderController::class)->group(function () {
  //order
  Route::get('select/laundry/{branch_id?}','selectlaundry');
  Route::get('category/items/{service_id?&category_id?&branch_id?}','getcategoryitems');
  Route::get('items/detailes{item_id?}','itemdetailes');
  Route::POST('submitorder','submitorder');
});
Route::group(['userApiAuth' => 'userApiAuth'],function(){
    Route::controller(AdressController::class)->group(function () {
        Route::POST('newadress','createadress');
        Route::get('delete/adress/{adress_id?}','deleteadress');
    });
});
