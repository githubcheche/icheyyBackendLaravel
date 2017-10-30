<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * 封装只查询目标关联的部分字段
     * scope重用查询方法前缀
     * @param $query
     * @param $relation 关联模型
     * @param array $columns 相关字段
     * @return mixed
     */
    public function scopeWithCertain($query, $relation, Array $columns)
    {
        return $query->with([$relation => function ($query) use ($columns){
            $query->select(array_merge(['id'], $columns));//array_merge合成为数组
        }]);
    }
}
