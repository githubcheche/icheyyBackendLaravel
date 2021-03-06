<?php

namespace App;

use Cache;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject as AuthenticatableUserContract;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable implements AuthenticatableUserContract
{
    use Notifiable, EntrustUserTrait;

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
    public function toggleLike($article_id)
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
    public function followeds()
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
     * 本用户切换关注一个用户
     * @param $followed_id 被关注者id
     * @return array
     */
    public function toggleFollow($followed_id)
    {
        return $this->followeds()->toggle($followed_id);//toggle,只有在多对多关系后使用，如果给定$followed_id已附加，就会被移除。同样的，如果给定$followed_id已移除，就会被附加
    }

    /**
     * 记录最后活跃时间
     */
    public function recordLastActivedAt()
    {
        // 这个 Redis 用于数据库更新，数据库每同步一次则清空一次该 Redis 。
        $data = Cache::get('actived_time_for_update');
        $data[$this->id] = Carbon::now()->toDateTimeString();
        Cache::forever('actived_time_for_update', $data);

        // 这个 Redis 用于读取，每次要获取活跃时间时，先到该 Redis 中获取数据。
        $data = Cache::get('actived_time');
        $data[$this->id] = Carbon::now()->toDateTimeString();
        Cache::forever('actived_time', $data);

    }


    /**
     * 获取最后的在线时间
     * @return mixed
     */
    public function lastActivedTime()
    {
        $data = \Cache::get('actived_time');
        if(empty($data[$this->id])){
            $data[$this->id] = $this->last_actived_at;
        }
        return $data[$this->id];
    }

    public function getIsOnLineAttribute()
    {
        $key = \Redis::hExists('USERS', $this->id);
        return $key;
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
