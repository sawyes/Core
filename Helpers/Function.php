<?php

if(! function_exists('array_trim')) {
    /**
     * trim
     *
     * @param array|string $datas
     *
     * @return array|string
     *
     */
    function array_trim($datas)
    {
        if (is_numeric($datas) || is_bool($datas) || is_resource($datas))
            return $datas;
        elseif (! is_array($datas))
            return trim($datas);

        return array_map('array_trim', $datas);
    }
}

if (! function_exists('object2array')) {
    /**
     * 对象转数组
     * @param $d
     * @return array
     */
    function object2array($d) {
        if (is_object($d))
            $d = get_object_vars($d);
        if (is_array($d))
            return array_map('object2array', $d);
        else
            return $d;
    }
}

if(! function_exists('assetWithVersion')) {
    /**
     * 给资源文件生成版本信息, 版本依据文件修改时间
     * @param string $path
     * @return string
     */
    function assetWithVersion($path)
    {
        $filePath = public_path($path);

        if (\File::exists($filePath)) {
            $time = \File::lastModified($filePath);
            return asset($path) . '?v=' . $time;
        }
        return asset($path);
    }
}

if (! function_exists('getBacktrace')) {

    /**
     * 调试trace
     *
     * @param int $ignore
     *
     * @return string
     *
     */
    function getBacktrace($ignore = 0) {

        $trace = '';

        foreach (debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 100) as $k => $v) {

            if (! isset($v['file']) || $k < $ignore) {
                continue;
            }

            if (is_array($v['args'])) {
                array_walk($v['args'], function (&$item, $key) use(&$trace) {

                    if (is_array($item) && ! empty($item) && isset($item[0]) && is_object($item[0])) {
                        if (isset($item[1]) && is_string($item[1])) {
                            $item = get_class($item[0]) . '::' . $item[1];
                        } else {
                            $item = get_class($item[0]);
                        }

                    } elseif($item instanceof \Illuminate\Routing\Route) {
                        $item = "Route::url ==> " . $item->uri;
                    } elseif($item instanceof \Exception) {
                        $trace .= PHP_EOL . "=====================================================" . PHP_EOL;
                        $trace .= PHP_EOL . "{$item->getFile()}({$item->getLine()})" .PHP_EOL .PHP_EOL;

                        $trace .= getFileLines($item->getFile(), $item->getLine()-3, $item->getLine()+3);

                        $trace .= PHP_EOL . "=====================================================" . PHP_EOL . PHP_EOL;
                    } else {
                        $item = json_encode($item);
                    }
                });

            }

            if (is_array($v['args']) && isset($v['args'][0]) && $v['args'][0] instanceof \Exception) {
                continue;
            }

            $args = implode(', ', $v['args']);
            $v['args'] = json_encode($v['args']);
            $trace .= '#' . ($k - $ignore) . ' ' . $v['file'] . '(' . $v['line'] . '): ' . (isset($v['class']) ? $v['class'] . '->' : '') . $v['function'] . '(' . $args . ')' . PHP_EOL;
        }

        return $trace;
    }
}

if (! function_exists('getFileLines')) {
    /**
     * 读取文件行
     *
     * @param        $filename
     * @param int    $startLine
     * @param int    $endLine
     * @param string $method
     *
     * @return string
     *
     */
    function getFileLines($filename, $startLine = 1, $endLine = 50, $method = 'rb')
    {
        $content = array ();

        $count = $endLine - $startLine;
        $fp    = new SplFileObject($filename, $method);
        $fp->seek($startLine - 1); // 转到第N行, seek方法参数从0开始计数
        for ($i = 0; $i <= $count; ++$i) {
            $content[] = $fp->current(); // current()获取当前行内容
            $fp->next(); // 下一行
        }

        return implode('', array_filter($content)); // array_filter过滤：false,null,''
    }
}

if (! function_exists('exception_log')) {

    /**
     * 异常追踪
     *
     * @param Exception $e
     * @param string    $handler
     *
     */
    function exception_log(\Exception $e, $handler = 'handler') {

        $message = PHP_EOL . "file:" . $e->getFile()
            . PHP_EOL . "line:" . $e->getLine()
            . PHP_EOL . "message:" . $e->getMessage()
            . PHP_EOL . "trance:"
            . PHP_EOL . getBacktrace();

        \Modules\Core\Helpers\LogHelper::write($message, [], $handler . '_Exception');
    }
}