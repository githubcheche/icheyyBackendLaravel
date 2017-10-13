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
    'middleware' => 'cors',
    'prefix' => 'v1',
], function() {
    // test
    Route::get('/cheyy', function (Request $request) {
        return 'hello cheyy';
    });

//    //Auth
//    Route::post('user/login', 'AuthController@login'); //登录认证

    //文章分类
    Route::resource('articles', 'Article\ArticlesController'); //所有文章
    Route::get('hot_articles', 'Article\ArticlesController@hotArticles'); //获取热门话题

    Route::resource('tags', 'Article\TagsController'); //标签
    Route::get('hot_tags', 'Article\TagsController@hotTags'); //获取分类标签
    Route::get('categories', 'Article\CategoriesController@index'); //获取文章的分类




});
