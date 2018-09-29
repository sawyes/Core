<?php

namespace Modules\Core\Helpers;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * 本类用于记录日志
 * sample:
 *   use App\Helpers\LogHelper;
 *   LogHelper::write('message',['name'=>'peter'],'filename');//每天一个日志文件
 *   LogHelper::writeSingle('message',['name'=>'peter'],'filename');//单个文件
 * Author: Pecwu
 * @version 2.0
 * Class LogHelper
 * @package App\Helpers
 */
class LogHelper
{
    /**
     * Detailed debug information
     */
    const DEBUG = 100;

    /**
     * Interesting events
     *
     * Examples: User logs in, SQL logs.
     */
    const INFO = 200;

    /**
     * Uncommon events
     */
    const NOTICE = 250;

    /**
     * Exceptional occurrences that are not errors
     *
     * Examples: Use of deprecated APIs, poor use of an API,
     * undesirable things that are not necessarily wrong.
     */
    const WARNING = 300;

    /**
     * Runtime errors
     */
    const ERROR = 400;

    /**
     * Critical conditions
     *
     * Example: Application component unavailable, unexpected exception.
     */
    const CRITICAL = 500;

    /**
     * Action must be taken immediately
     *
     * Example: Entire website down, database unavailable, etc.
     * This should trigger the SMS alerts and wake you up.
     */
    const ALERT = 550;

    /**
     * Urgent alert.
     */
    const EMERGENCY = 600;


    /**
     * Logging levels from syslog protocol defined in RFC 5424
     *
     * @var array $levels Logging levels
     */
    protected static $levels = array(
        self::DEBUG     => 'DEBUG',
        self::INFO      => 'INFO',
        self::NOTICE    => 'NOTICE',
        self::WARNING   => 'WARNING',
        self::ERROR     => 'ERROR',
        self::CRITICAL  => 'CRITICAL',
        self::ALERT     => 'ALERT',
        self::EMERGENCY => 'EMERGENCY',
    );

    /**
     * @var array $handlers 日志句柄
     */
    protected static $handlers = [];

    /**
     * 将日志写入文件，程序将自动每天一个文件，并且可以指定删除多久以前的日志文件
     *
     * @param string $message   记录内容
     * @param array  $context   附加内容
     * @param string $file_name 保存文件名称
     * @param string $level     日志等级
     *                          Author: Pecwu
     */
    public static function write($message = '', array $context = [], $file_name = '', $level = 'INFO')
    {
        self::log($message, $context, $file_name, $level);
    }

    /**
     * 将日志写入文件，程序将自动每天一个文件，并且可以指定删除多久以前的日志文件
     *
     * @param string $message       记录内容
     * @param array  $context       附加内容
     * @param string $file_name     保存文件名称
     * @param string $level         日志等级
     *                              Author: Pecwu
     */
    private static function log($message = '', array $context = [], $file_name = '', $level = 'INFO')
    {
        $level = strtoupper($level);
        // 添加调用者信息
        $trace     = debug_backtrace(false, 2)[1];
        if (empty( $file_name)) {
            $file_name = basename($trace['file']);
        }

        $format_message = vsprintf("file: %s line: %d message: %s", [
            basename($trace['file']),
            $trace['line'],
            $message,
        ]);

        if (! self::hasHandler($file_name)) {
            self::makeHandler($file_name);
        }

        // 获得日志句柄
        $logger = self::getHandler($file_name);

        // 记录日志
        $logger->{'add'. ucfirst(strtolower($level))}($format_message, $context);
    }

    /**
     * 日志句柄不存在
     *
     * @param $file_name
     *
     * @return bool
     *
     */
    public static function hasHandler($file_name)
    {
        return isset(self::$handlers[$file_name]);
    }

    /**
     * 获取当前日志句柄
     *
     * @param $file_name
     *
     * @return Logger
     */
    private static function getHandler($file_name)
    {
        return self::$handlers[$file_name];
    }

    /**
     * 生成文件保存路径
     *
     * @param string $file_name 文件名称
     *
     * @return string 文件保存路径
     */
    private static function generatePath($file_name)
    {
        return storage_path('logs/') . $file_name . '-' . date('Y-m-d') . '.log';
    }

    /**
     * make file handler
     * @param $file_name
     */
    private static function makeHandler($file_name)
    {
        //生成日志保存路径
        $save_path = self::generatePath($file_name);
        $log       = new Logger(config('app.env'));

        $streamHandler = new StreamHandler($save_path,   Logger::INFO);
        $streamHandler->setFormatter(new LineFormatter(null, null, true, true));
        $handlers  = $log->pushHandler($streamHandler);

        self::$handlers[$file_name] = $handlers;
    }
}
