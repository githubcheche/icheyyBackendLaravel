<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 * 文章类别
 * @package App
 */
class Category extends Model
{
    protected $fillable = ['name'];

    /**
     * 获取文章类别的所有文章
     * 一对多
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
