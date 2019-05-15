<?php

namespace Modules\Core\Helpers;

use Matrix\Exception;

class DingdingHelper
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var array
     */
    private $webhooks;

    /**
     * @var string
     */
    private $webhook;

    /**
     * DingdingRepo constructor.
     */
    public function __construct($webhooksConfig)
    {
        $this->client = new \GuzzleHttp\Client();
        $this->loadConfig($webhooksConfig);
    }

    /**
     * 加载默认配置
     *
     * @param $webhooksConfig
     *
     * @throws Exception
     *
     */
    public function loadConfig($webhooksConfig)
    {
        if (empty($webhooksConfig)) {
            throw new Exception('webhooks config miss, load failed. ' . __FILE__ . ':' . __LINE__);
        }
        $this->webhooks = $webhooksConfig;

        $firstRowKey = array_keys($webhooksConfig);

        if (!isset($firstRowKey[0])) {
            throw new Exception('webhooks config miss, load failed. ' . __FILE__ . ':' . __LINE__);
        }

        $this->webhook($firstRowKey[0]);
    }

    /**
     * set webhook
     *
     * @param $name
     *
     * @return $this
     * @throws Exception
     *
     */
    public function webhook($name = '')
    {
        if (isset($this->webhooks[$name])) {
            $this->webhook = $this->webhooks[$name];
        } else {
            dd($name);
            throw new Exception('webhooks not find ' . $name);
        }

        return $this;
    }

    /**
     * 消息类型及数据格式 markdown类型
     * https://open-doc.dingtalk.com/microapp/serverapi2/qf2nxq#-6
     *
     * @param string $title
     * @param string $message
     * @param string $url 钉钉机器人钩子
     *
     * @return array
     *
     */
    public function markdown($title = 'Dingtalk', $message = '', $url = '')
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
     * @param $message
     * @param $url
     *
     * @return array
     *
     */
    public function text($message, $url = '')
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
        $url = ! empty($url) ? $url: $this->webhook;

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