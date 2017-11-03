<?php

namespace App\Http\Controllers;

use App\Article;
use App\Comment;
use App\User;
use Auth;
use Cache;
use Illuminate\Http\Request;
use Validator;

class UsersController extends Controller
{    public function __construct()
{
    $this->middleware('my.jwt.auth', [
        'only' => ['editPassword', 'avatarUpload', 'update']
    ]);
}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     * 查看指定用户信息
     * GET /user/{id}
     * @param  int  $id 用户id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (empty($user = Cache::get('users_cache' . $id))) {
            $user = User::findOrFail($id);// 查找指定id用户
            Cache::put('users_cache' . $id, $user, 10);
        }
        return $this->responseSuccess('查询成功', $user);
    }

    /**
     * 查找指定用户的所有文章
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function userArticles($id)
    {
        if (empty($articles = Cache::get('user_articles' . $id))) {
            $articles = Article::where('user_id', $id)->latest('created_at')->get();
            Cache::put('user_articles' . $id, $articles, 10);
        }
        return $this->responseSuccess('查询成功', $articles);
    }

    /**
     * 查找指定用户的所有回复
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function userReplies($id)
    {
        if (empty($comments = Cache::get('user_replies' . $id))) {
            $comments = Comment::where('user_id', $id)->with('commentable')->latest('created_at')->get()->toArray();
//            $comments = $this->commentTransformer->transformCollection($comments);//？？？？？？？？？？
            Cache::put('user_replies' . $id, $comments, 10);
        }
        return $this->responseSuccess('查询成功', $comments);
    }

    /**
     * 查找指定用户点赞的所有文章
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function userLikesArticles($id)
    {
        if (empty($articles = Cache::get('user_likes_articles' . $id))) {
            $articles = User::where('id', $id)->first()->likes()->latest('created_at')->get();
            Cache::put('user_likes_articles' . $id, $articles, 10);
        }
        return $this->responseSuccess('查询成功', $articles);
    }

    /**
     * 修改密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editPassword(Request $request)
    {
        // 验证密码
        $validator = Validator::make($request->all(), [
            'password' => 'required|between:6,16|confirmed'
        ]);

        if ($validator->fails()) {
            return $this->responseError('表单验证失败', $validator->errors()->toArray());
        }

        Auth::user()->update(['password' => request('password')]);
        return $this->responseSuccess('密码重置成功');
    }


    /**
     * 头像更新
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function avatarUpload(Request $request)
    {
        $file = $request->file('file');
        $filename = md5(time()) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('image'), $filename);
        $avatar_image = env('API_URL') . '/image/' . $filename;// 存储路径
        $user = Auth::user();
        $user->avatar = $avatar_image;
        $user->save();
        return $this->responseSuccess('修改成功', ['avatar' => $avatar_image]);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * 本用户信息修改
     * PUT/PATCH /users/{id}
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = ['real_name' => $request->get('real_name'), 'city' => $request->get('city')];
        $user = Auth::user();
        $user->real_name = request('real_name');
        $user->city = request('city');
        $user->save();

        return $this->responseSuccess('个人信息修改成功', $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
