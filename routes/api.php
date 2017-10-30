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
    'middleware' => ['cors'],
    'prefix' => 'v1',
], function () {
    // test
    Route::get('/cheyy', function (Request $request) {
        return 'hello cheyy';
    });

    //Auth
    Route::any('user/login', 'AuthController@login'); //登录认证
    Route::post('user/register', 'AuthController@register'); //注册
    Route::get('user/logout', 'AuthController@logout'); //退出


    //文章分类
    Route::resource('articles', 'Article\ArticlesController'); //文章
    Route::get('hot_articles', 'Article\ArticlesController@hotArticles'); //获取热门话题
    Route::post('content_image', 'Article\ArticlesController@contentImage'); //上传文章图片

    Route::resource('tags', 'Article\TagsController'); //标签
    Route::get('hot_tags', 'Article\TagsController@hotTags'); //获取分类标签

    Route::get('categories', 'Article\CategoriesController@index'); //获取文章的分类


    //用户相关
    Route::get('article/is_like','Article\LikesController@isLike'); // +/id(article_id) 用户是否点赞了一个话题
    Route::get('article/like','Article\LikesController@likeThisArticle'); //用户点赞一个话题
    Route::get('user/is_follow','Article\FollowsController@isFollow'); //+/id(user_id) 用户是否关注一个用户
    Route::get('user/follow','Article\FollowsController@followThisUser'); //+/id(user_id) 用户关注一个用户


});

