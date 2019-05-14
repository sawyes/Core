<?php

namespace Modules\Core\Helpers;

class DingdingHelper
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * DingdingRepo constructor.
     */
    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
    }

    /**
     * 消息类型及数据格式 markdown类型
     * https://open-doc.dingtalk.com/microapp/serverapi2/qf2nxq#-6
     *
     * @param string $url 钉钉机器人钩子
     * @param string $title
     * @param string $message
     *
     * @return array
     *
     */
    public function markdown($url, $title = 'Dingtalk', $message = '')
    {
        $body = [
            'msgtype' => 'markdown',
            'markdown' => [
                'title' => $title ,
                'text' =>  $message,
            ],
        ];

        return $this->post($url, $body);
    }

    /**
     * 消息类型及数据格式 text类型
     * @doc https://open-doc.dingtalk.com/microapp/serverapi2/qf2nxq#-4
     *
     * @param $url
     * @param $message
     *
     * @return array
     *
     */
    public function text($url, $message)
    {
        $body = [
            'msgtype' => 'text',
            'text' => [
                'content' => $message ,
            ],
        ];

        return $this->post($url, $body);
    }

    /**
     * get method send message to dingtalk
     *
     * @param $url
     * @param $body
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     *
     */
    private function post($url, $body)
    {
        return $this->send('POST', $url, $body);
    }

    /**
     * send message to dingtalk
     *
     * @param string $method
     * @param        $url
     * @param        $body
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws
     */
    private function send($method = 'GET', $url, $body)
    {
        throw_if(empty($url),'Missing dingtalk Url', [
            'url'  => $url,
            'body' => $body,
        ]);

        return $this->client->request($method, $url, [
            'json' => $body
        ]);
    }

}