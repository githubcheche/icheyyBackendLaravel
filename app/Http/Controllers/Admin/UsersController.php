<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * UsersController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * 获取用户列表
     * GET /users
     * /users/?filter=
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $users = $this->userRepository->getUserList($request);
        return $this->responseSuccess('OK', $users->toArray());
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
     * 获取指定用户
     * Display the specified resource.
     * GET /users/{id}
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->userRepository->getUser($id);
        return $this->responseSuccess('OK', $user->toArray());
    }

    /**
     * 更新用户数据
     * Update the specified resource in storage.
     * PUT/PATCH /users/{id}
     * /users/{id}/?roles=角色描述参数
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = $this->userRepository->updateUser($request, $id);
        return $this->responseSuccess('修改成功', $user);
    }

    /**
     * 删除用户
     * Remove the specified resource from storage.
     *
     * DELETE /users/{id}
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->userRepository->deleteUser($id)) {
            return $this->responseSuccess('删除成功');
        };

        return $this->responseError('删除失败');
    }
}
