<?php

namespace App\Http\Controllers\Article;

use App\Article;
use App\Category;
use App\Http\Controllers\Controller;
use App\Tag;

use Carbon\Carbon;
use Image;
use Validator;
use Illuminate\Http\Request;

/**
 * Class ArticlesController 资源控制器
 * 使用php artisan make:controller ArticlesController --resource --model=Articles 生成的
 * @package App\Http\Controllers
 */
class ArticlesController extends Controller
{
    public function __construct()
    {
        $this->middleware('my.jwt.auth', [
            'only' => ['store', 'update', 'destroy', 'contentImage']
        ]);
    }

    /**
     * 获取所有文章
     * GET /articles
     * /articles?tag=
     * @param Request $request
     * @return string
     */
    public function index(Request $request)
    {
        $page = 1;

        // 取得page参数
        if ($request->has('page')) {
            $page = $request->input('page');
        }

        // 获取当前页文章
        $articles = $this->getArticles($page, $request);

        if (!empty($articles)) {
            return $this->responseSuccess('OK', $articles);
        }

        return $this->responseError('查询失败');
    }

    //GET /articles/create
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     * 查看指定文章
     * GET /articles/{id}
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $article = $this->getArticle($id);

        if (!empty($article)) {
            return $this->responseSuccess('OK', $article->toArray());
        }

        return $this->responseError('查询失败');
    }


    /**
     * 创建保存文章
     * POST /articles
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|between:4,100',
            'body' => 'required|min:10',
            'tag' => 'required',
            'is_hidden' => 'required',
            'category' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->responseError('表单验证失败', $validator->errors()->toArray());
        }

        $tags = $this->normalizeTopics($request->get('tag'));
        $data = [
            'title' => $request->get('title'),
            'body' => $request->get('body'),
            'cover' => $request->get('cover'),
            'user_id' => \Auth::id(),
            'is_hidden' => $request->get('is_hidden'),
            'category_id' => $request->get('category'),
            'last_comment_time' => Carbon::now(),
        ];
        $article = Article::create($data);
        $article->increment('category_id');
        Category::find($request->get('category'))->increment('articles_count');
        \Auth::user()->increment('articles_count');
        $article->tags()->attach($tags);
        \Cache::tags('articles')->flush();
        return $this->responseSuccess('OK', $article);
    }


    /**
     * Show the form for editing the specified resource.
     * 显示编辑文章
     * GET /articles/{id}/edit
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *  编辑更新文章
     *  PUT/PATCH /articles/{id}
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|between:4,100',
            'body' => 'required|min:10',
            'tag' => 'required',
            'is_hidden' => 'required',
            'category' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->responseError('表单验证失败', $validator->errors()->toArray());
        }

        $tags = $this->normalizeTopics($request->get('tag'));
        $data = [
            'title' => $request->get('title'),
            'body' => $request->get('body'),
            'cover' => $request->get('cover'),
            'is_hidden' => $request->get('is_hidden'),
            'category_id' => $request->get('category'),
        ];
        $article = Article::find($id);//取出文章
        $article->update($data);//更新文章
        $article->tags()->sync($tags);//更新tag
        \Cache::tags('articles')->flush();//清除文章cache缓存
        return $this->responseSuccess('OK', $article);
    }

    /**
     * Remove the specified resource from storage.
     * 删除文章
     * DELETE /articles/{id}
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * 获取热门文章
     * @return \Illuminate\Http\JsonResponse
     */
    public function hotArticles()
    {
        if (empty($hotArticles = \Cache::get('hotArticles_cache'))) {
            //按评论数排序，取１０个
            $hotArticles = Article::where([])->orderBy('comments_count', 'desc')->latest('updated_at')->take(10)->get();
            \Cache::put('hotArticles_cache', $hotArticles, 10);
        }
        return $this->responseSuccess('查询成功', $hotArticles);
    }

    /**
     * 文章图片上传
     * 文件名为时间的md5值加后缀
     * 存放在/storage/app/public/articleImage下
     * 返回值图片文件路径
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function contentImage(Request $request)
    {
        $file = $request->file('image');
        if( !filesize($file) ) {
            return $this->responseError('上传图片文件为空');
        }
        $extension = $file->getClientOriginalExtension();
        if(empty($extension)) {
            return $this->responseError('上传图片文件后缀为空');
        }
        $filename = md5(time()) . '.' . $extension;
        $file->move(public_path('../storage/app/public/articleImage'), $filename);
        $article_image = env('API_URL') . '/storage/articleImage/' . $filename;
        return $this->responseSuccess('查询成功', ['url' => $article_image]);
    }

    /**
     * 上传封面
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function coverImage(Request $request)
    {
        $file = $request->file('file');
        $filename = md5(time()) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('../storage/app/public/articleImage/cover'), $filename);
        Image::configure(array('driver' => 'imagick'));
        Image::make(public_path('../storage/app/public/articleImage/cover/' . $filename))->fit(300, 200)->save();
        $article_image = env('API_URL') . '/storage/articleImage/cover/'.$filename;
        return $this->responseSuccess('查询成功', ['url' => $article_image]);
    }

    /**
     * 搜索
     * @return \Illuminate\Http\JsonResponse
     */
    public function search()
    {
        $articles = Article::search(request('q'), null, true)->with('user')->paginate(10);
        return $this->responseSuccess('查询成功', $articles);
    }

/////////////////////////////////////////////////////////////////////////////////////

    /**
     * 取得所有未隐藏的文章
     * 若是有tag参数，返回tag相同的文章
     * @param $page
     * @param $request
     * @return mixed
     */
    public function getArticles($page, $request)
    {
//        Cache::tags('articles')->flush();
        if (empty($request->tag)) {//没有tag参数
            return \Cache::tags('articles')->remember('articles' . $page, $minutes = 10, function () {
                return Article::notHidden()->with('user', 'tags', 'category')->latest('created_at')->paginate(15);
            });
        } else {
            return \Cache::tags('articles')->remember('articles' . $page . $request->tag, $minutes = 10, function () use ($request) {
                //查找有tags的文章，并且tag表中的name与url中的tag参数相同
                return Article::notHidden()->whereHas('tags', function ($query) use ($request) {
                    $query->where('name', $request->tag);
                })->with('user', 'tags', 'category')->latest('created_at')->paginate(15);
            });
        }
    }

    /**
     * 取得指定文章id的文章
     * @param $id
     * @return mixed
     */
    public function getArticle($id)
    {
        $article = Article::where('id', $id);
        $article->increment('view_count', 1);//查看数加1
        return $article->with('user', 'tags', 'category')->first();
    }

    /**
     * 返回tag的id数组
     * @param $tags
     * @return array
     */
    public function normalizeTopics($tags)
    {
        // collect创建集合实例，map循环每个元素
        return collect($tags)->map(function ($tag) {
            if (is_numeric($tag)) {//是数字
                Tag::find($tag)->increment('articles_count');//articles_count自增1
                return (int)$tag;
            }
            // tag是字符串，创建新的tag
            $newTag = Tag::create(['name' => $tag, 'articles_count' => 1]);
            return $newTag->id;
        })->toArray();
    }




}