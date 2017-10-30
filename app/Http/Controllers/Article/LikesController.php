<?php

namespace App\Http\Controllers\Article;


use App\Article;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LikesController extends Controller
{
    public function __construct()
    {
        $this->middleware('my.jwt.auth');
    }


    /**
     * 用户是否点赞了这个话题
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function isLike(Request $request)
    {
        $user =  \Auth::user();//取得现在登录的用户对象
        $liked = $user->hasLikedThis($request->get('id'));//是否为本用户点赞文章
        if ($liked) {
            // 有点赞
            return $this->responseSuccess('OK', ['liked' => true]);
        }
        return $this->responseSuccess('OK', ['liked' => false]);
    }


    /**
     * 切换点赞该话题
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function likeThisArticle(Request $request)
    {
        $user =  \Auth::user();
        $article  = Article::where('id', ($request->get('id')))->first();//获取文章
        $liked = $user->likeThis($article->id);//切换点赞文章
        if (count($liked['detached']) > 0) { //如果是取消点赞
            $user->decrement('likes_count');
            $article->decrement('likes_count');
            return $this->responseSuccess('OK', ['liked' => false]);
        }
        // 增加点赞
        $data = [
            'name' => $user->name,
            'user_id' => $user->id,
            'title' => $article->title,
            'title_id' => $article->id
        ];
//        $article->user->notify(new LikeArticleNotification($data));//通知
        $user->increment('likes_count');
        $article->increment('likes_count');
        return $this->responseSuccess('OK', ['liked' => true]);
    }
}
