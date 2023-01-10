<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\Auth\AuthController;
use App\Http\Controllers\User\AdressController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\LaundryController;



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
Route::controller(HomeController::class)->group(function () {
   Route::get('slider','getslider');
   Route::get('top/laundry/{lat?&lon?}','toplaundries');
});
Route::controller(LaundryController::class)->group(function () {
    Route::get('laundryinfo/{branch_id?}','laundryinfo');
 });
Route::controller(OrderController::class)->group(function () {
  //order
  Route::get('services','getservices');
  Route::get('select/laundry/{services?}','selectlaundry');
  Route::get('choose/laundry/{branch_id?}','chooselaundry');
  Route::get('category/items/{service_id?&category_id?&branch_id?}','getcategoryitems');
  Route::get('items/detailes{item_id?}','itemdetailes');
});
Route::group(['middleware' => 'userApiAuth'],function(){
    Route::controller(AdressController::class)->group(function () {
        Route::POST('newadress','createadress');
        Route::POST('updateaddress','updateaddress');
        Route::get('delete/adress/{adress_id?}','deleteadress');
    });
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/edit/profile','edit');
        Route::get('/edit/profile/phone','editphone');
        Route::post('update/profile','update');
        Route::post('profile/updatepassword','updatepassword');
        Route::post('profile/updatepassword','updatepassword');
        Route::post('profile/updatephone','updatephone');
        Route::post('verified/phone','verifyphone');
        Route::get('user/adress','getaddresses');
    });
    Route::controller(OrderController::class)->group(function () {
        Route::POST('submitorder','submitorder');
        Route::POST('checkout','checkout');
    });
});
