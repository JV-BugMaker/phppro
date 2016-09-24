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
        var_dump($next);
        echo '验证csrf-token'.PHP_EOL;
        $next();
    }
}

class ShareErrorsFromSession implements Middleware
{
    public static function handle(Closure $next)
    {
        var_dump($next);
        // TODO: Implement handle() method.
        echo '如果session中有error变量,则共享它'.PHP_EOL;
        $next();
    }
}

class StartSession implements Middleware
{
    public static function handle(Closure $next)
    {
        var_dump($next);

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
        var_dump($next);

        // TODO: Implement handle() method.
        $next();
        echo '添加下一次请求需要的cookie'.PHP_EOL;
    }
}

class EncryptCookie implements Middleware
{
    public static function handle(Closure $next)
    {
        var_dump($next);

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
        var_dump($next);

        // TODO: Implement handle() method.
        echo '确定当前程序是否处于维护状态'.PHP_EOL;
        $next();
    }
}
//返回一个回调函数 封装了一个管道处理函数
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
//
//确定当前程序是否处于维护状态
//对输入请求的cookie加密
//开启session,获取数据
//如果session中有error变量,则共享它
//验证csrf-token
//请求想路由器传递,返回响应
//保存数据,关闭session添加下一次请求需要的cookie
//对输出响应的

//
//object(Closure)#7 (1) {
//["static"]=>
//  array(2) {
//    ["stack"]=>
//    object(Closure)#6 (1) {
//    ["static"]=>
//      array(2) {
//        ["stack"]=>
//        object(Closure)#5 (1) {
//        ["static"]=>
//          array(2) {
//            ["stack"]=>
//            object(Closure)#4 (1) {
//            ["static"]=>
//              array(2) {
//                ["stack"]=>
//                object(Closure)#3 (1) {
//                ["static"]=>
//                  array(2) {
//                    ["stack"]=>
//                    object(Closure)#1 (0) {
//                    }
//                    ["pipe"]=>
//                    string(15) "VerifyCsrfToken"
//                  }
//                }
//                ["pipe"]=>
//                string(22) "ShareErrorsFromSession"
//              }
//            }
//            ["pipe"]=>
//            string(12) "StartSession"
//          }
//        }
//        ["pipe"]=>
//        string(25) "AddQueueCookiesToResponse"
//      }
//    }
//    ["pipe"]=>
//    string(13) "EncryptCookie"
//  }
//}
//确定当前程序是否处于维护状态
//object(Closure)#6 (1) {
//["static"]=>
//  array(2) {
//    ["stack"]=>
//    object(Closure)#5 (1) {
//    ["static"]=>
//      array(2) {
//        ["stack"]=>
//        object(Closure)#4 (1) {
//        ["static"]=>
//          array(2) {
//            ["stack"]=>
//            object(Closure)#3 (1) {
//            ["static"]=>
//              array(2) {
//                ["stack"]=>
//                object(Closure)#1 (0) {
//                }
//                ["pipe"]=>
//                string(15) "VerifyCsrfToken"
//              }
//            }
//            ["pipe"]=>
//            string(22) "ShareErrorsFromSession"
//          }
//        }
//        ["pipe"]=>
//        string(12) "StartSession"
//      }
//    }
//    ["pipe"]=>
//    string(25) "AddQueueCookiesToResponse"
//  }
//}
//对输入请求的cookie加密
//object(Closure)#5 (1) {
//["static"]=>
//  array(2) {
//    ["stack"]=>
//    object(Closure)#4 (1) {
//    ["static"]=>
//      array(2) {
//        ["stack"]=>
//        object(Closure)#3 (1) {
//        ["static"]=>
//          array(2) {
//            ["stack"]=>
//            object(Closure)#1 (0) {
//            }
//            ["pipe"]=>
//            string(15) "VerifyCsrfToken"
//          }
//        }
//        ["pipe"]=>
//        string(22) "ShareErrorsFromSession"
//      }
//    }
//    ["pipe"]=>
//    string(12) "StartSession"
//  }
//}
//object(Closure)#4 (1) {
//["static"]=>
//  array(2) {
//    ["stack"]=>
//    object(Closure)#3 (1) {
//    ["static"]=>
//      array(2) {
//        ["stack"]=>
//        object(Closure)#1 (0) {
//        }
//        ["pipe"]=>
//        string(15) "VerifyCsrfToken"
//      }
//    }
//    ["pipe"]=>
//    string(22) "ShareErrorsFromSession"
//  }
//}
//开启session,获取数据
//object(Closure)#3 (1) {
//["static"]=>
//  array(2) {
//    ["stack"]=>
//    object(Closure)#1 (0) {
//    }
//    ["pipe"]=>
//    string(15) "VerifyCsrfToken"
//  }
//}
//如果session中有error变量,则共享它
//object(Closure)#1 (0) {
//}
//验证csrf-token
//请求想路由器传递,返回响应
//保存数据,关闭session添加下一次请求需要的cookie
//对输出响应的