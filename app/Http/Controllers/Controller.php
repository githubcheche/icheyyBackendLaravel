<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 状态
     * ＝１成功
     *
     * @var int
     */
    protected $status = 1;

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * 设置状态
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * 错误返回
     * @param $message
     * @param array|NULL $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseError($message, $data = NULL)
    {
        return $this->setStatus(0)->response($message, $data);
    }

    /**
     * 返回成功信息
     * @param $message
     * @param array|NULL $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseSuccess($message, $data = NULL)
    {
        return $this->response($message, $data);
    }

    /**
     * 返回信息格式json
     * @param $message
     * @param array|NULL $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function response($message, $data = NULL)
    {
        return response()->json([
            'status' => $this->getStatus(),
            'message' => $message,
            'data' => $data,
        ]);
    }
}
