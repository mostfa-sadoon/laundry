<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\driver\AuthController;
use App\Http\Controllers\driver\driverController;
use App\Http\Controllers\driver\OrderController;
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
    Route::post('login','login');
    Route::post('sendtotp','sendtotp');
});

Route::group(['middleware' => 'driverApiAuth'],function(){
    Route::controller(AuthController::class)->group(function () {
        Route::get('logout','logout');
    });
    Route::controller(driverController::class)->group(function () {
        Route::get('update/status','updatestatus');
        Route::get('get/driverinfo','driverinfo');
        Route::post('update/info','updateinfo');
        Route::post('update/phone','updatephone');
    });

    Route::controller(OrderController::class)->group(function () {
        Route::get('new/order','getneworder');
        Route::get('acceptorder/order{order_id?}','Acceptorder');
        Route::get('reject/order{order_id?}','rejectorder');
        Route::get('order/info{order_id?}','orderinfo');
        Route::get('order/inprogress','inprogressorder');
        Route::get('order/confirm/pickup/{order_id?&confirm_type}','confirmorder');
    });
});
