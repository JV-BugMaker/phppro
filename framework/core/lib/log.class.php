<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/14
 * Time: 下午9:19
 * 日志类文件
 */
namespace core\lib;
use core\lib\Config;
class Log
{
    //确定日志存储方式
    //写日志
    public static $class;
    public static function init(){
        //配置文件
        $conf = Config::get('DRIVER','log');
        $driver = '\core\lib\driver\log\\'.ucfirst($conf);
        self::$class = new $driver;
        //实例化操作之后  需要具体 写入
    }

    public static function log($name){
        self::$class->log($name);
    }
}