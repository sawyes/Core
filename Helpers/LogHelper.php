<?php

namespace Modules\Core\Helpers;

use Illuminate\Log\Writer;
use Monolog\Logger;

/**
 * 本类用于记录日志
 * sample:
 *   use App\Helpers\LogHelper;
 *   LogHelper::write('message',['name'=>'peter'],'filename');//每天一个日志文件
 *   LogHelper::writeSingle('message',['name'=>'peter'],'filename');//单个文件
 * Author: Pecwu
 * @version 1.0
 * Class LogHelper
 * @package App\Helpers
 */
class LogHelper
{
    /**
     * 可用的日志等级
     * @var array
     */
    protected static $levels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];

    protected static $logger = null;

    protected static $logger_name = 'BI';
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
     * @param int    $days      删除多少天以前的日志
     *                          Author: Pecwu
     */
    public static function write($message = '', array $context = [], $file_name = 'addata', $level = 'debug', $days = 7)
    {
        self::log($message, $context, $file_name, $level, true, $days);
    }

    /**
     * 把日志写入到单个文件
     *
     * @param string $message
     * @param array  $context
     * @param string $file_name
     * @param string $level
     * Author: Pecwu
     */
    public static function writeSingle($message = '', array $context = [], $file_name = 'addata', $level = 'debug')
    {
        self::log($message, $context, $file_name, $level, false);
    }

    /**
     * 将日志写入文件，程序将自动每天一个文件，并且可以指定删除多久以前的日志文件
     *
     * @param string $message       记录内容
     * @param array  $context       附加内容
     * @param string $file_name     保存文件名称
     * @param string $level         日志等级
     * @param bool   $useDaliyFiles 是否每天记录
     * @param int    $days          删除多少天以前的日志
     *                              Author: Pecwu
     */
    private static function log($message = '', array $context = [], $file_name = 'addata', $level = 'debug', $useDaliyFiles = true, $days = 7)
    {
        // 添加调用者信息
        $trace     = debug_backtrace(false, 2)[1];
        $write_msg = "file: " . basename($trace['file'])
            ." line:" . $trace['line']
            ." message: " . $message;

        //生成日志保存路径
        $save_path = self::generatePath($file_name);

        //检查错误登记
        if (!in_array($level, self::$levels)) {
            $level = 'debug';
        }

        if (!key_exists($file_name, self::$handlers)) {
            // 插件新的日志句柄
            $handlers = self::makeHandle();

            // 设置文件路径, 并且确认是否按日期分割文件
            if ($useDaliyFiles) {
                $handlers->useDailyFiles($save_path, $days, $level);
            } else {
                $handlers->useFiles($save_path, $level);
            }

            // 添加日志句柄
            self::pushHandler($file_name, $handlers);
        }

        // 获得日志句柄
        $logger = self::getHandler($file_name);
        // 记录日志
        $logger->log($level, $write_msg, $context);
    }

    /**
     * 获取当前日志句柄
     *
     * @param $file_name
     *
     * @return mixed
     * Author: Pecwu
     */
    private static function getHandler($file_name)
    {
        return self::$handlers[$file_name];
    }

    /**
     * 添加日志句柄
     *
     * @param string $file_name
     * @param Writer $handlers
     * Author: Pecwu
     */
    private static function pushHandler($file_name, $handlers)
    {
        self::$handlers[$file_name] = $handlers;
    }

    /**
     * 创建一个日志示例句柄
     * @return Writer
     * Author: Pecwu
     */
    private static function makeHandle()
    {
        return new Writer(new Logger(config('app.env', 'local')));
    }


    /**
     * 生成文件保存路径
     *
     * @param string $file_name 文件名称
     *
     * @return string 文件保存路径
     * Author: Pecwu
     */
    private static function generatePath($file_name)
    {
        return storage_path() . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $file_name . '.log';
    }
}
