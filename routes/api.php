<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('register','userController@registerUser');

Route::post('login','userController@login');

Route::group(['middleware' => ['jwt.verify']], function() {

    Route::prefix('product')->group(function () {

       Route::post('create','ProductController@store');
       Route::post('update','ProductController@update');
       Route::delete('delete/{id}','ProductController@delete');

    });

    Route::prefix('cart')->group(function () {

       Route::post('add','CartController@add');
       Route::post('update','CartController@update');
       Route::post('remove','CartController@remove');
       Route::post('delete','CartController@delete');
       Route::post('myCar','CartController@myCar');

    });
});

