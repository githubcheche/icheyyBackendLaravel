<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     * new对象时可填充字段
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'confirm_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     * 隐藏返回字段
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];











    public function setPasswordAttribute($password)
    {
        return $this->attributes['password'] = \Hash::make($password);
    }

    public function likes()
    {
        return $this->belongsToMany(Article::class, 'likes')->withTimestamps();
    }

    //用户点赞一个话题
    public function likeThis($article)
    {
        return $this->likes()->toggle($article);
    }

    //用户是否点赞了这个话题
    public function hasLikedThis($article)
    {
        return $this->likes()->where('article_id', $article)->count();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers() // 关注其他人
    {
        return $this->belongsToMany(self::class, 'followers', 'follower_id', 'followed_id')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followersUser() // 被其他人关注
    {
        return $this->belongsToMany(self::class, 'followers', 'followed_id', 'follower_id')->withTimestamps();
    }

    /**
     * @param $user
     * @return array
     */
    public function followThisUser($user)
    {
        return $this->followers()->toggle($user);
    }


}
