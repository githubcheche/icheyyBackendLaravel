<?php

namespace App\Http\Controllers;

use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

use JWTAuth;
use Mail;
use Naux\Mail\SendCloudTemplate;
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
            'avatar' => env('API_URL') . '/image/avatar.jpeg',
            'password' => $request->get('password'),
            'confirm_code' => str_random(60),
        ];

        $user = User::create($newUser);
        $this->sendVerifyEmailTo($user);
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
            if ($user->is_confirmed == 0) {
                return $this->responseError('您还未激活该账号，请先前往邮箱激活');
            }
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

    /**
     * 验证邮件发送
     * @param $user
     */
    private function sendVerifyEmailTo($user)
    {
        $data = [ 'url' => 'http://www.icheyy.top/#/verify_email/' . $user->confirm_code,
            'name' => $user->name ];
        $template = new SendCloudTemplate('cheyy_verify', $data);

        Mail::raw($template, function ($message) use ($user) {
            $message->from('root@icheyy.top', 'icheyy.top');
            $message->to($user->email);
        });
    }

    /**
     * 验证邮箱并激活账户
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyToken()
    {
        $user = User::where('confirm_code', Request('code'))->first();
        if (empty($user)) {
            return $this->responseError('激活失败');
        }
        $user->is_confirmed = 1;
        $user->confirm_code = str_random(60);
        $user->save();
        Auth::login($user);

        $token = JWTAuth::fromUser($user);
        $user->jwt_token = [
            'access_token' => $token,
            'expires_in' => Carbon::now()->addMinutes(config('jwt.ttl'))->timestamp
        ];
        return $this->responseSuccess('注册成功', $user);
    }

}


