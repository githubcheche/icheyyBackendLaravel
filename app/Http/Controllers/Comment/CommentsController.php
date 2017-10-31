<?php

namespace App\Http\Controllers\Comment;

use App\Article;
use App\Comment;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class CommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('my.jwt.auth', [
            'only' => ['store']
        ]);
    }

    /**
     * 获取指定文章的评论并携带评论者信息
     * @param $commentable_id 多态关联表主键id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($commentable_id)
    {
        $article = Article::where('id',$commentable_id)->first();
        $comments = $article->comments()->where('commentable_id', $commentable_id)->where('parent_id', 0)->with(['user' => function ($query) {
            $query->select('id', 'name', 'avatar');
        }])->get();
        return $this->responseSuccess('OK', $comments);
    }

    /**
     *
     * @param $commentable_id 多态关联表主键id
     * @return \Illuminate\Http\JsonResponse
     */
    public function childComments($commentable_id)
    {
        $parent_id = Request('parent_id');
        $new_comments = [];// 评论集
        $this->getChildComments($commentable_id, $parent_id, $new_comments);
        return $this->responseSuccess('查询成功', $new_comments);
    }


    /////////////////////////////////////////////////////////////////

    /**
     * 取得所有子评论
     * @param $commentable_id 顶级评论关联模型id（如article_id）
     * @param $parent_id    父评论id
     * @param $new_comments 评论集容器
     */
    protected function getChildComments($commentable_id, $parent_id, &$new_comments)
    {
        // 获取所有子评论
        $article = Article::where('id',$commentable_id)->first();
        $comments = $article->comments()->where('commentable_id', $commentable_id)->where('parent_id', $parent_id)
            ->with(['user' => function ($query) {
                $query->select('id', 'name');
            }])->get();

        if (! empty($comments)) {// 如果有子评论
            foreach ($comments as $comment) {
                $parent = Comment::where('id', $comment['parent_id'])->first()->user()->first();// 父级评论者
                $comment['parent_name'] = $parent->name;//增加父级评论者姓名
                $comment['parent_user_id'] = $parent->id;
                $new_comments[] = $comment;// 把评论加入容器
                $this->getChildComments($commentable_id, $comment['id'], $new_comments);// 递归调用
            }
        }
    }

    /**
     * 提交评论
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $user = \Auth::user();
        // 创建评论
        $comment = Comment::create([
            'commentable_id' => request('article_id'),
            'commentable_type' => 'App\Article',
            'user_id' => $user->id,
            'parent_id' => request('parent_id'),
            'body' => request('body'),
        ]);
        if (! empty($comment)) {//创建成功，
            $user->increment('comments_count');
            $article = Article::where('id', request('article_id'));
            $article->increment('comments_count');
            // 更新最后评论信息
            $article->update([
                'last_comment_user_id' => $user->id,
                'last_comment_time' => Carbon::now(),//获取现在时间
            ]);

            // 获取此条评论，伴随评论用户信息
            $comment = Comment::where('id', $comment->id)->with(['user' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }])->first();
//            $article = $article->first();//提取被评论文章
//            $data = [
//                'name' => $user->name,
//                'user_id' => $user->id,
//                'title' => $article->title,
//                'title_id' => $article->id,
//                'comment' => $comment->body
//            ];
            if ($comment->parent_id !== 0) {// 有父评论
                $parent = Comment::where('id', $comment->parent_id)->first()->user()->first();
                $comment->parent_name = $parent->name;//提取父评论信息
                $comment->parent_user_id = $parent->id;
//                $parent->notify(new CommentArticleNotification($data));
            } else {
//                $article->user->notify(new CommentArticleNotification($data));
            }

            return $this->responseSuccess('OK', $comment);
        }
        return $this->responseError('Has Something Wrong');
    }
}
