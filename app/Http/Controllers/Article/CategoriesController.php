<?php

namespace App\Http\Controllers\Article;


use App\Category;
use App\Http\Controllers\Controller;

class CategoriesController extends Controller
{
    /**
     *　获取所有类别
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Category::pluck('name', 'id')->toArray();//获取一列值

        $data = [];
        foreach ($categories as $key => $category) {
            $data[] = ['id' => $key, 'name' => $category];
        }
        return $this->responseSuccess('OK', $data);
    }
}
