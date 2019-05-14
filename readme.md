# 核心模块

* log sql service provider
* Helpers/Function.php
* ResponesTraits
* notify dingding


#### log sql service provider

开启sql日志监听

.env

```
CORE_SQL_LOG=true
```

#### Function

如何添加自定义function?

修改`module.json`

```
"files": [
    "start.php",
    "Helpers/Function.php"
],
```

#### ResponesTraits

计划用于继承控制器

规范控制异步返回数据

#### dingding

add alias

```
'Dingding' => Modules\Core\Facades\Dingding::class,
```

sendmessage

```
\Dingding::text('webhook_url', 'notfyt message')
```
