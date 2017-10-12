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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
//    'middleware' => 'cors',
    'prefix' => 'v1',
], function() {
    // test
    Route::get('/cheyy', function (Request $request) {
        return 'hello cheyy';
    });

    //Auth
    Route::post('user/login', 'AuthController@login'); //登录认证

    //文章分类
    Route::resource('articles', 'ArticlesController'); //所有文章
    Route::get('categories', 'CategoriesController@index'); //获取文章的分类
});
