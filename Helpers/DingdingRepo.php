<?php

namespace Modules\Core\Helpers;

class DingdingRepo
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var
     */
    protected $logger = 'Dingding_exception';

    /**
     * DingdingRepo constructor.
     */
    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
    }

    /**
     * send exception
     *
     * @param \Exception $exception
     *
     */
    public function exception(\Exception $exception)
    {
        if (app()->environment() != 'production') {
            return ;
        }

        $file = $exception->getFile();

        \Cache::remember(md5($file), $min=config('dingtalk.dingding.timeout', 10), function () use ($exception){

            $clientIp = request()->getClientIp();
            $project = request()->server('HTTP_HOST');
            $message = "## {$project} 异常提醒\n\n"
                ."> clientIp: {$clientIp}\n\n"
                ."> file:{$exception->getFile()}\n\n"
                ."> code: {$exception->getCode()}\n\n"
                ."> line: {$exception->getLine()}\n\n"
                ."> message: {$exception->getMessage()}";

            $body = $this->markdown('异常提醒', $message);

            $this->post(config('dingtalk.dingding.exception'), $body);

            return true;
        });
    }

    /**
     * send markdown message
     *
     * @param string $title
     * @param string $message
     *
     * @return array
     *
     */
    public function markdown($title = 'Dingtalk', $message = '')
    {
        $body = [
            'msgtype' => 'markdown',
            'markdown' => [
                'title' => $title ,
                'text' =>  $message,
            ],
        ];

        return $body;
    }

    /**
     * send text
     *
     * @param $message
     *
     * @return array
     *
     */
    public function text($message)
    {
        $body = [
            'msgtype' => 'text',
            'text' => [
                'content' => $message ,
            ],
        ];

        return $body;
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
    public function post($url, $body)
    {
        return $this->send('POST', $url, $body);
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
    public function get($url, $body)
    {
        return $this->send('GET', $url, $body);
    }

    /**
     * send message to dingtalk
     *
     * @param string $method
     * @param        $url
     * @param        $body
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     *
     */
    public function send($method = 'GET', $url, $body)
    {
        if (empty($url)) {
            LogHelper::write('Missing dingtalk Url', [], $this->logger);
            return true;
        }

        return $this->client->request($method, $url, [
            'json' => $body
        ]);
    }

}