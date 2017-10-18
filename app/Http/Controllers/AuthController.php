<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        // jwt_auth 认证中间件
        $this->middleware('my.jwt.auth', [
            'only' => ['logout']
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users|between:4,12',
            'email' => 'required|email|unique:users',
            'password' => 'required|between:6,16|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->responseError('表单验证失败', $validator->errors()->toArray());
        }

        $newUser = [
            'email' => $request->get('email'),
            'name' => $request->get('name'),
//            'avatar' => env('API_URL') . '/image/avatar.jpeg',
            'avatar' => 'no',
            'password' => $request->get('password'),
            'confirm_code' => str_random(60),
        ];

        $user = User::create($newUser);
//        $this->sendVerifyEmailTo($user);
//        $user->attachRole(3);

        return $this->responseSuccess('感谢您支持Cheyy小镇，请前往邮箱激活该用户');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required|between:6,16',
        ]);

        if ($validator->fails()) {
            return $this->responseError('表单验证失败', $validator->errors()->toArray());
        }

        $field = filter_var($request->get('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $credentials = array_merge([
            $field => $request->get('login'),
            'password' => $request->get('password'),
        ]);

        try {
            // 创建令牌
            if (! $token = \JWTAuth::attempt($credentials)) {
                return $this->responseError('用户名或密码错误');
            }
            $user = \Auth::user();
//            if ($user->is_confirmed == 0) {
//                return $this->responseError('您还未激活该账号，请先前往邮箱激活');
//            }
            // 设置JWT令牌
            $user->jwt_token = [
                'access_token' => $token,
                'expires_in' => Carbon::now()->addMinutes(config('jwt.ttl'))->timestamp
            ];
            return $this->responseSuccess('登录成功', $user->toArray());
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return $this->responseError('无法创建令牌');
        }
    }

    public function logout()
    {
        try {
            \JWTAuth::parseToken()->invalidate();
        } catch (TokenBlacklistedException $e) {
            return $this->responseError('令牌已被列入黑名单');
        } catch (JWTException $e) {
            // 忽略该异常（Authorization为空时会发生）
        }
        return $this->responseSuccess('登出成功');
    }

}


