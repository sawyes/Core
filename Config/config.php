<?php

/*
|--------------------------------------------------------------------------
| config.php => config('core')
|--------------------------------------------------------------------------
|
| CoreServiceProvider::registerConfig()
|
| config('core.name')
|    ==> Core
|
*/

return [
    'name' => 'Core',
    
    'sql_log' => env('CORE_SQL_LOG', false),
];
