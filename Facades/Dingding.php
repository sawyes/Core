<?php

namespace Modules\Core\Facades;

use Illuminate\Support\Facades\Facade;

class Dingding extends Facade
{
    /**
     * 获取组件注册名称
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'notify.dingding';
    }
}