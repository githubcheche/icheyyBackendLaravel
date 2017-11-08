<?php

namespace App\Repositories;

use App\User;
use App\Role;

/**
 * 用户表仓库操作
 * Class UserRepository
 * @package App\Repositories
 */
class UserRepository
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var Role
     */
    protected $role;

    /**
     * UserRepository constructor.
     * @param User $user
     * @param Role $role
     */
    public function __construct(User $user, Role $role)
    {
        $this->user = $user;
        $this->role = $role;
    }

    /**
     * 获取用户列表
     * filter查找指定名称参数
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUserList($request)
    {
        if (!empty($name = $request->filter)) {// 有过滤参数
            return $this->user->where('name', 'like', "%$name%")->with([
                'roles' => function($query) {
                    $query->select('description');
                }])->paginate($request->paginate);
        }

        // 查找全部用户
        return $this->user->with([
            'roles' => function($query) {
                $query->select('description');
            }])->paginate($request->paginate);
    }

    /**
     * 获取指定用户
     * @param $id
     * @return mixed
     */
    public function getUser($id)
    {
        $user = $this->user->find($id);
        $roles = $user->roles()->pluck('description');
        return $roles;
    }

    /**
     * 更新指定用户数据
     * @param $request
     * @param $id
     * @return mixed
     */
    public function updateUser($request, $id)
    {
        $user = $this->user->findOrFail($id);

        if($request->roles) {//有?roles=角色描述参数，则更新角色参数
            $user->detachRoles($user->roles); //清除以前的角色

            if (is_array($request->roles)) {//写入新角色
                $roles = [];
                foreach ($request->roles as $description) {
                    $roles[] = $this->role->where('description', $description)->first();
                }
                $user->attachRoles($roles);
            }
        } else {// 更新其他参数
            $user->is_confirmed = $request->is_confirmed;// 激活
            $user->is_banned = $request->is_banned;// 禁用
            $user->save();
        }

        return $this->user->where('id', $id)->with([
            'roles' => function($query) {
                $query->select('name');
            }])->first()->toArray();
    }

    /**
     * 删除指定用户
     * @param $id
     * @return bool
     */
    public function deleteUser($id)
    {
        $user = $this->user->find($id);

        if ($user) {
            $user->roles()->sync([]);//清空有关此用户的所有角色
            $user->destroy($id);
            return true;
        }

        return false;
    }
}