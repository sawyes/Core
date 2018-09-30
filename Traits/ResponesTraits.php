<?php

namespace Modules\Core\Traits;

use Illuminate\Pagination\LengthAwarePaginator;

trait ResponesTraits
{
    /**
     * 操作 - 成功
     *
     * @param string  $message
     * @param array   $data
     * @param int     $code
     * @param int     $status
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function success($message = 'successful', $data = [], $code = 200, $status = 200)
    {
        $data = [
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ];

        return \Response::json($data, $status);
    }

    /**
     * 操作 - 失败
     * 7xx - 自定义错误
     *
     * @param string    $message
     * @param array     $data
     * @param int       $code       业务标识
     * @param int       $status     HTTP HEADER STATUS
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function fail($message = 'fail', $data = [], $code = 700, $status = 200)
    {
        $data = [
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ];

        return \Response::json($data, $status);
    }

    /**
     * 格式化输出list
     *
     * @param $data
     * @param int $total
     * @param string $message
     * @param int $code
     * @param int $status
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function toList($data, $total = 0, $message = 'successful', $code = 200, $status = 200)
    {
        // 如果通过 paginate 获取的模型数据, 可以不传入 $total
        if ($data instanceof LengthAwarePaginator) {
            $data =  [
                'code'            => $code,
                'message'         => $message,
                'data'            => $data->toArray()['data'],
                'recordsFiltered' => $data->total(),
            ];

            return \Response::json($data, $status);
        }

        if ($total === 0) {
            $total = count($data);
        }

        $data = [
            'code'            => $code,
            'message'         => $message,
            'data'            => $data,
            'recordsFiltered' => $total,
        ];

        return \Response::json($data, $status);
    }
}