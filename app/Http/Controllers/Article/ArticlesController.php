<?php

namespace App\Http\Controllers;

use App\Article;
use App\Category;
use Auth;
use Illuminate\Support\Facades\Cache;
use Validator;
use Illuminate\Http\Request;

/**
 * Class ArticlesController 资源控制器
 * 使用php artisan make:controller ArticlesController --resource --model=Articles 生成的
 * @package App\Http\Controllers
 */
class ArticlesController extends Controller
{
    public function __construct(){}

    /**
     * 获取所有文章
     * GET /articles
     * /articles?tag=
     * @param Request $request
     * @return string
     */
    public function index(Request $request){
        $page = 1;

        // 取得page参数
        if ($request->has('page')) {
            dd('page='.$page);
            $page = $request->input('page');
        }

        // 获取当前页文章
        $articles = $this->getArticles($page, $request);

        if (! empty($articles)) {
            return $this->responseSuccess('OK', $articles);
        }

        return $this->responseError('查询失败');
    }

    //GET /articles/create
    public function create(){}

    /**
     * 保存文章
     * POST /articles
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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

        $tags = $this->articleRepository->normalizeTopics($request->get('tag'));
        $data = [
            'title' => $request->get('title'),
            'body' => $request->get('body'),
            'user_id' => Auth::id(),
            'is_hidden' => $request->get('is_hidden'),
            'category_id' => $request->get('category'),
        ];
        $article = $this->articleRepository->create($data);
        $article->increment('category_id');
        Category::find($request->get('category'))->increment('articles_count');
        Auth::user()->increment('articles_count');
        $article->tags()->attach($tags);
        Cache::tags('articles')->flush();
        return $this->responseSuccess('OK', $article);
    }

    /**
     * Display the specified resource.
     * 查看指定文章
     * GET /articles/{id}
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $article = $this->getArticle($id);

        if (! empty($article)) {
            return $this->responseSuccess('OK', $article->toArray());
        }

        return $this->responseError('查询失败');
    }

    /**
     * Show the form for editing the specified resource.
     * 显示编辑文章
     * GET /articles/{id}/edit
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){}

    /**
     * Update the specified resource in storage.
     *  编辑更新文章
     *  PUT/PATCH /articles/{id}
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
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

        $tags = $this->articleRepository->normalizeTopics($request->get('tag'));
        $data = [
            'title' => $request->get('title'),
            'body' => $request->get('body'),
            'is_hidden' => $request->get('is_hidden'),
            'category_id' => $request->get('category'),
        ];
        $article = $this->articleRepository->byId($id);
        $article->update($data);
        $article->tags()->sync($tags);
        Cache::tags('articles')->flush();
        return $this->responseSuccess('OK', $article);
    }

    /**
     * Remove the specified resource from storage.
     * 删除文章
     * DELETE /articles/{id}
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
        if (empty($request->tag)) {//没有tag参数
            return Cache::tags('articles')->remember('articles' . $page, $minutes = 10, function() {
                return Article::notHidden()->with('user'/*, 'tags', 'category'*/)->latest('created_at')->paginate(30);
            });
        } else {
            return Cache::tags('articles')->remember('articles' . $page . $request->tag, $minutes = 10, function() use ($request) {
                //查找有tags的文章，并且tag表中的name与url中的tag参数相同
                return Article::notHidden()->whereHas('tags', function ($query) use ($request) {
                    $query->where('name', $request->tag);
                })->with('user'/*, 'tags', 'category'*/)->latest('created_at')->paginate(30);
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
        return $article->with('user'/*, 'tags' ,'category'*/)->first();
    }



    public function hotArticles()
    {
        if (empty($hotArticles = Cache::get('hotArticles_cache'))) {
            $hotArticles = Article::where([])->orderBy('comments_count', 'desc')->latest('updated_at')->take(10)->get();
            Cache::put('hotArticles_cache', $hotArticles, 10);
        }
        return $this->responseSuccess('查询成功', $hotArticles);
    }

    function contentImage(Request $request)
    {
        $file = $request->file('image');
        $filename = md5(time()) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('../storage/app/public/articleImage'), $filename);
        $article_image = env('API_URL') . '/storage/articleImage/'.$filename;
        return $this->responseSuccess('查询成功', ['url' => $article_image]);
    }

}