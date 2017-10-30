<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject as AuthenticatableUserContract;

class User extends Authenticatable implements AuthenticatableUserContract
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

    /**
     * 加密密码
     * @param $password
     * @return string
     */
    public function setPasswordAttribute($password)
    {
        return $this->attributes['password'] = \Hash::make($password);
    }

    /**
     * 获取用户点赞的所有文章
     * 多用户对多文章
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likes()
    {
        return $this->belongsToMany(Article::class, 'likes')->withTimestamps();
    }


    /**
     * 用户切换点赞一个话题
     * @param $article_id
     * @return array
     */
    public function likeThis($article_id)
    {
        return $this->likes()->toggle($article_id);//toggle如果给定 ID 已附加，就会被移除。同样的，如果给定 ID 已移除，就会被附加
    }

    /**
     * 用户是否点赞了这个话题
     * @param $article_id
     * @return int 本用户本文章点赞个数
     */
    public function hasLikedThis($article_id)
    {
        return $this->likes()->where('article_id', $article_id)->count();
    }


    /**
     * 关注其他人，获取本用户关注的所有人
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany(self::class, 'followers', 'follower_id', 'followed_id')->withTimestamps();
    }

    /**
     * 被其他人关注，获取关注本用户的所有人
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followersUser()
    {
        return $this->belongsToMany(self::class, 'followers', 'followed_id', 'follower_id')->withTimestamps();
    }

    /**
     * 用户切换关注一个话题
     * @param $user_id
     * @return array
     */
    public function followThisUser($user_id)
    {
        return $this->followers()->toggle($user_id);//toggle如果给定 ID 已附加，就会被移除。同样的，如果给定 ID 已移除，就会被附加
    }








    /**
     * JWT
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Eloquent model method
    }

    /**
     * JWT
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }














}
