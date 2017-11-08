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
    Route::get('verify_email', 'AuthController@verifyToken'); //验证注册码



    //文章分类
    Route::resource('articles', 'Article\ArticlesController'); //文章
    Route::get('hot_articles', 'Article\ArticlesController@hotArticles'); //获取热门话题
    Route::post('content_image', 'Article\ArticlesController@contentImage'); //上传文章图片
    Route::post('article_cover_image', 'Article\ArticlesController@coverImage'); //上传封面图片
    Route::get('search', 'Article\ArticlesController@search');// 搜索文章

    Route::resource('tags', 'Article\TagsController'); //标签
    Route::get('hot_tags', 'Article\TagsController@hotTags'); //获取分类标签

    Route::get('categories', 'Article\CategoriesController@index'); //获取文章的分类

    Route::get('articles/{article}/comments', 'Comment\CommentsController@index'); //获取文章的评论
    Route::get('articles/{article}/child_comments', 'Comment\CommentsController@childComments'); // +/?parent_id=(comment_id)  获取文章的子评论
    Route::post('comments', 'Comment\CommentsController@store'); //增加文章的评论

    //用户相关
    Route::get('article/is_like','Article\LikesController@isLike'); // +/?id=(article_id) 用户是否点赞了一个话题
    Route::get('article/like','Article\LikesController@likeThisArticle'); //+/?id=(article_id) 切换用户点赞一个话题

    Route::get('user/is_follow','Article\FollowsController@isFollow'); //+/?id=(user_id) 用户是否关注一个用户
    Route::get('user/follow','Article\FollowsController@followThisUser'); //+/?id=(user_id) 切换用户关注一个用户

    Route::resource('users', 'UsersController');// users/id 查询指定用户； users/ 修改个人信息
    Route::get('users/{user}/articles', 'UsersController@userArticles'); //用户发表的文章
    Route::get('users/{user}/replies', 'UsersController@userReplies'); //用户的回复
    Route::get('users/{user}/likes','UsersController@userLikesArticles'); //用户所有点赞话题
//    Route::post('edit_user_info', 'UsersController@editUserInfo');
    Route::post('edit_password', 'UsersController@editPassword'); //修改密码
    Route::post('avatar/upload', 'UsersController@avatarUpload'); //上传头像

});

/*
|--------------------------------------------------------------------------
| 后台管理的 API 接口
|--------------------------------------------------------------------------
*/
//Route::group([
//    'middleware' => 'cors',
//    'namespace' => 'Admin',
//    'prefix' => 'v1/admin',
//], function() {
//    Route::post('login', 'AuthController@login'); //后台登录
//});

Route::group([
    'middleware' => ['cors', 'jwt.auth', 'check.permission'],
    'namespace' => 'Admin',
    'prefix' => 'v1/admin',
], function() {
    Route::get('menu', 'MenusController@getSidebarTree')->name('users.menu'); //获取后台左侧菜单
    Route::get('group_permissions', 'PermissionsController@groupPermissions'); //获取权限组
    Route::resource('roles', 'RolesController');
    Route::resource('users', 'UsersController');
    Route::resource('menus', 'MenusController');
    Route::resource('permissions', 'PermissionsController');
    Route::get('logout', 'LoginController@logout');
});