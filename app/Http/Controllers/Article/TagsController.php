<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Tag;
use Illuminate\Support\Facades\Cache;


class TagsController extends Controller
{
    public function __construct()
    {
    }

    /**
     * 获取所有标签
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (empty($tags = Cache::get('Tags_cache'))) {//缓存是空的
            $tags = Tag::select('id', 'name')->get();
//            $tags = DB::table('tags')->select('id', 'name')->get();
            Cache::put('Tags_cache', $tags, 10);
        }
        return $this->responseSuccess('查询成功', $tags);
    }

    /**
     * 获取热门标签
     * @return \Illuminate\Http\JsonResponse
     */
    public function hotTags()
    {
        if (empty($hotTags = Cache::get('hotTags_cache'))) {
            $hotTags = Tag::where([])->orderBy('articles_count', 'desc')->take(30)->get();
            Cache::put('hotTags_cache', $hotTags, 10);
        }
        return $this->responseSuccess('查询成功', $hotTags);
    }
}
