<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Comment 评论模型
 * @package App
 */
class Comment extends Model
{
    protected $fillable = ['user_id', 'body', 'commentable_id', 'commentable_type', 'parent_id'];

    /**
     * 获得拥有此评论的模型。
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function commentable()
    {
        return $this->morphTo();//多态关联，就是评论又可以关联文章表，也可以关联其他的表，如给问题页面增加评论，则可以关联问题表
    }

    /**
     * 获取此评论的评论用户
     * 一对一
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取此评论的所有子评论
     * 一对多
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childComments()
    {
        // foreignKey参数是第一个参数对象的关联键字段，这个字段是本对象的主键
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
