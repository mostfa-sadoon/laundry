<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\branch\Auth\AuthController;
use App\Http\Controllers\branch\ServiceController;
use  App\Http\Controllers\branch\closeingdaycontroller;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::controller(AuthController::class)->group(function () {
        Route::post('branch/login','login');
});



Route::group(['middleware' => 'lundryApiAuth'],function(){
    Route::controller(AuthController::class)->group(function () {
    Route::post('branch/register','registration');
    });
    Route::controller(ServiceController::class)->group(function () {
       Route::post('branch/services/set/itemprice','setitemprice');
       Route::post('branch/aditionalservices/set/itemprice','setaditionalserviceprice');
    });
});
Route::controller(closeingdaycontroller::class)->group(function () {
    Route::get('branch/closingday','getcloseingday');
});
Route::controller(ServiceController::class)->group(function () {
    Route::get('branch/services','getservices');
    Route::get('branch/aditionalservices{branch_id?}','getaditionalservices');
});




Route::group(['middleware' => 'branchApiAuth'],function(){
    Route::controller(ServiceController::class)->group(function () {
       Route::get('get/branch/services','branchservices');
    });
    Route::controller(AuthController::class)->group(function () {
        Route::get('branch/logout','logout');
    });
});


