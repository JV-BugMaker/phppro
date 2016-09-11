<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/11
 * Time: 下午10:34
 * 框架的入口文件 作用先是定义一些常量以及启动框架
 */

//注意这边的realpath 传入/是无效的  需要./
define('VIVI',realpath('./'));
define('CORE',VIVI.'/core');
define('APP',VIVI.'/app');
define('DEBUG',true);

if(DEBUG){
    ini_set('display_error','On');
}else{
    ini_set('display_error','Off');
}
include CORE.'/common/function.php';
include CORE.'/vivi.class.php';
\core\Vivi::run();

