<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class article 文章模型
 * @package App
 */
class Article extends Model
{
    /**
     * The attributes that are mass assignable.
     * new对象时可填充字段
     * @var array
     */
    protected $fillable = [
        'title', 'body', 'user_id','last_comment_time', 'cover',
    ];

    /**
     *　获取用户的所有文章
     *  一对一
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 限制寻找 帖子没有被隐藏
     * @param $query
     * @return mixed
     */
    public function scopeNotHidden($query)
    {
        return $query->where('is_hidden', 'F');
    }

    /**
     * 取得文章的所有tags
     * 多对多
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    /**
     * 取得文章的类别
     * 一对一
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    /**
     * 获得此文章的所有评论。
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * 获取这篇文章的评论以 parent_id 来分组
     * @return static
     */
    public function getComments()
    {
        return $this->comments()->with('user')->get()->groupBy('parent_id');
    }

}
