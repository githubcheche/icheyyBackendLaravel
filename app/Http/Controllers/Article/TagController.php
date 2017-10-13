<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class TagController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (empty($tags = Cache::get('Tags_cache'))) {//缓存是空的
            $tags = DB::table('tags')->select('id', 'name')->get();
            Cache::put('Tags_cache', $tags, 10);
        }
        return $this->responseSuccess('查询成功', $tags);
    }

    public function hotTags()
    {
        if (empty($hotTags = Cache::get('hotTags_cache'))) {
            $hotTags = Tag::where([])->orderBy('articles_count', 'desc')->take(30)->get();
            Cache::put('hotTags_cache', $hotTags, 10);
        }
        return $this->responseSuccess('查询成功', $hotTags);
    }
}
