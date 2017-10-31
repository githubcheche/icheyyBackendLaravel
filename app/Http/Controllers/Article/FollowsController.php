<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class FollowsController extends Controller
{
    public function __construct()
    {
        $this->middleware('my.jwt.auth');
    }

    /**
     * 用户是否关注了这个用户
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function isFollow(Request $request)
    {
        $user = \Auth::user();//获取本用户
        $followers = $user->followeds()->pluck('followed_id')->toArray();//取得本用户所有被关注者的id
        if (in_array($request->get('id'), $followers)) {//比对传入的用户id，检查本用户是否关注了传入用户
            return $this->responseSuccess('OK', ['followed' => true]);
        }
        return $this->responseSuccess('OK', ['followed' => false]);
    }

    /**
     * 切换关注此用户
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function followThisUser(Request $request)
    {
        $user = \Auth::user();
        $userToFollow = User::find($request->get('id'));
        $followed = $user->toggleFollow($userToFollow->id);//切换本用户关注此用户状态,返回状态

        if ( count($followed['attached']) > 0 ) {//关注此用户
            $user->increment('followings_count');
            $userToFollow->increment('followers_count');
//            $userToFollow->notify(new FollowUserNotification(['name' => $user->name, 'user_id' => $userToFollow->id]));
            return $this->responseSuccess('OK', ['followed' => true]);
        }
        $user->decrement('followings_count');
        $userToFollow->decrement('followers_count');
        return $this->responseSuccess('OK', ['followed' => false]);
    }
}
