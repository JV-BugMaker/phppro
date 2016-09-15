<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/11
 * Time: 下午10:34
 * 框架的入口文件 作用先是定义一些常量以及启动框架
 */
date_default_timezone_set('PRC');
//注意这边的realpath 传入/是无效的  需要./
define('VIVI',realpath('./'));
define('CORE',VIVI.'/core');
define('APP',VIVI.'/app');
define('MODULE','app');
define('DEBUG',true);

//首先引入vendor核心 自动加载文件
include "vendor/autoload.php";

if(DEBUG){
    //实例化错误显示页面
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
    ini_set('display_error','On');
}else{
    ini_set('display_error','Off');
}

dump($_SERVER);die;
//asdasdasdasd();
include CORE.'/common/function.php';
include CORE.'/vivi.class.php';
//类自动加载
spl_autoload_register('\core\Vivi::load');
\core\Vivi::run();

