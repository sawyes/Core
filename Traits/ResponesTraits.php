<?php

namespace Modules\Core\Traits;

use Illuminate\Pagination\LengthAwarePaginator;

trait ResponesTraits
{
    /**
     * 操作成功
     *
     * @param       $message
     * @param array $data
     * @param $status
     *
     * @return array
     *
     */
    public function success($message, $data = [], $status = 200)
    {
        return [
            'successful' => true,
            'message'    => $message,
            'data'       => $data,
            'status'     => $status,
        ];
    }

    /**
     * 操作失败
     *
     * @param $message
     * @param array $data
     * @param int $status
     *
     * @return array
     *
     */
    public function fail($message, $data = [], $status = 400)
    {
        $response = [
            'successful' => false,
            'data' => [],
            'message'    => $message,
            'status'    => $status,
        ];

        if (! empty($data)) {
            $response['data'] = $data;
        }

        return $response;
    }

    /**
     * 格式化输出list
     *
     * @param $data
     * @param $total
     *
     * @return array
     *
     */
    public function toList($data, $total = 0)
    {
        // 如果通过 paginate 获取的模型数据, 可以不传入 $total
        if ($data instanceof LengthAwarePaginator) {
            return [
                'successful' => true,
                'data' => $data->toArray()['data'],
                'recordsFiltered' => $data->total(),
            ];
        }

        if ($total == 0) {
            $total = count($data);
        }

        return [
            'successful' => true,
            'data' => $data,
            'recordsFiltered' => $total,
        ];
    }

    /**
     * 返回Item
     *
     * @param mixed $data
     *
     * @return array
     *
     */
    public function toItem($data)
    {
        return [
            'successful' => true,
            'data' => $data
        ];
    }
}