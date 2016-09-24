<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 2016/9/24
 * Time: 下午5:15
 */

interface Middleware
{
    public static function handle(Closure $next);
}

class VerifyCsrfToken implements Middleware
{
    public static function handle(Closure $next)
    {
        // TODO: Implement handle() method.
        echo '验证csrf-token'.PHP_EOL;
        $next();
    }
}

class ShareErrorsFromSession implements Middleware
{
    public static function handle(Closure $next)
    {
        // TODO: Implement handle() method.
        echo '如果session中有error变量,则共享它'.PHP_EOL;
        $next();
    }
}

class StartSession implements Middleware
{
    public static function handle(Closure $next)
    {
        // TODO: Implement handle() method.
        echo '开启session,获取数据'.PHP_EOL;
        $next();
        echo '保存数据,关闭session';
    }
}

class AddQueueCookiesToResponse implements Middleware
{
    public static function handle(Closure $next)
    {
        // TODO: Implement handle() method.
        $next();
        echo '添加下一次请求需要的cookie'.PHP_EOL;
    }
}

class EncryptCookie implements Middleware
{
    public static function handle(Closure $next)
    {
        // TODO: Implement handle() method.
        echo '对输入请求的cookie加密'.PHP_EOL;
        $next();
        echo '对输出响应的'.PHP_EOL;
    }
}

class CheckForMaintenanceMode implements Middleware
{
    public static function handle(Closure $next)
    {
        // TODO: Implement handle() method.
        echo '确定当前程序是否处于维护状态'.PHP_EOL;
        $next();
    }
}

function getSlice()
{
    return function($stack,$pipe){
        return function() use($stack,$pipe){
            return $pipe::handle($stack);
        };
    };
}


function then()
{
    $pipes = [
        "CheckForMaintenanceMode",
        "EncryptCookie",
        "AddQueueCookiesToResponse",
        "StartSession",
        "ShareErrorsFromSession",
        "VerifyCsrfToken"
    ];
    $firstSlice = function (){
        echo '请求想路由器传递,返回响应'.PHP_EOL;
    };
    $pipes = array_reverse($pipes);
    call_user_func(
        array_reduce($pipes,getSlice(),$firstSlice)
    );
}

then();