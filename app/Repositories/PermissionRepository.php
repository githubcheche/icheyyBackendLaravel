<?php

namespace App\Repositories;

use App\Permission;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * 权限表仓库操作
 * Class PermissionRepository
 * @package App\Repositories
 */
class PermissionRepository
{
    use ValidatesRequests;
    /**
     * @var Permission
     */
    protected $permission;

    /**
     * RoleRepository constructor.
     * @param Permission $permission
     */
    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    /**
     * 取得权限列表
     * filter查找指定名称参数
     * @param $request
     * @return mixed
     */
    public function getPermissionList($request)
    {
        if (!empty($name = $request->filter)) {
            return $this->permission->where('display_name', 'like', "%$name%")->paginate($request->paginate);
        }

        return $this->permission->paginate($request->paginate);
    }

    /**
     * 获取权限组
     * @return mixed
     */
    public function groupPermissions()
    {
        $permissions = $this->permission->orderBy('name', 'desc')->get();
        $array = [];
        foreach ($permissions as $permission) {
            array_set($array, $permission->name, $permission);
        }

        return $array;
        //return $permissions->toArray();
    }

    /**
     * 取得指定权限
     * @param $id
     * @return mixed
     */
    public function getPermission($id)
    {
        $permission =  $this->permission->find($id);
        return $permission->toArray();
    }

    /**
     * 创建权限
     * @param $request
     * @return mixed
     */
    public function createPermission($request)
    {
        //验证数据
        if(method_exists($this->permission, 'rules')) {
            $this->validate($request,
                $this->permission->rules(),
                $this->permission->messages());
        }

        $permission =  $this->permission->create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->display_name,
            'uri' => $request->uri
        ]);

        return $permission->toArray();
    }

    /**
     * 修改权限
     * @param $request
     * @param $id
     * @return mixed
     */
    public function updatePermission($request, $id)
    {
        //验证数据
        if(method_exists($this->permission, 'rules')) {
            $this->validate($request,
                $this->permission->rules($id),
                $this->permission->messages());
        }

        $permission = $this->permission->find($id);
        $permission->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->display_name,
            'uri' => $request->uri
        ]);

        return $permission->toArray();
    }

    /**
     * 删除权限
     * @param $id
     * @return bool
     */
    public function deletePermission($id)
    {
        $permission = $this->permission->find($id);

        if (!empty($permission)) {
            $permission->roles()->sync([]);
            $permission->destroy($id);
            return true;
        }

        return false;
    }
}