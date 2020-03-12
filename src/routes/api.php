<?php

use Illuminate\Http\Request;

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

<<<<<<< HEAD
//Route::middleware('net:api')->get('/net', function (Request $request) {
//    return $request->user();
//});
=======
Route::middleware('net:api')->get('/net', function (Request $request) {
    return $request->user();
});
>>>>>>> 6e2d425a8213bab7dc9ec77436d23924d8931e6b
