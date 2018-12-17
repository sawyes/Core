<?php

namespace Modules\Core\Helpers;

use \GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * @doc https://guzzle-cn.readthedocs.io/zh_CN/latest/
 */
class HttpHelper
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;



    /**
     * HttpHelper constructor.
     *
     * @param Client $client
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    public function raw($url, $body, $headers = [])
    {
        if (empty($headers)) {
            $headers = [
                'Content-Type' => 'application/json',
                'User-Agent'   => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36',
            ];
        }

        $response = $this->client->post($url, [
            'headers' => $headers,
            'body'=>$body
        ]);

        return [$response->getStatusCode(), $response->getBody()->getContents()];
    }

    public function download($url, $output)
    {
        $respone = $this->client->get($url);

        $fp=fopen($output,"w");

        fputs($fp, $respone->getBody());

        fclose($fp);

        return true;
    }

    /**
     * 单文件异步下载 $promise->wait() 控制异步等待
     *
     * @param $url
     * @param $output
     *
     * @return \GuzzleHttp\Promise\PromiseInterface $promise
     *
     */
    public function downloadAsync($url, $output)
    {
        $promise = $this->client->requestAsync('GET', $url);

        $promise->then(function($respone) use($output){
                $fp=fopen($output,"w");

                fputs($fp, $respone->getBody());

                fclose($fp);
            },function(RequestException $e){
                echo $e->getMessage() . "\n";
                echo $e->getRequest()->getMethod();
            }
        );

        return $promise;
    }
}