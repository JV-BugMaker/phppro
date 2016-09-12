<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/11
 * Time: 下午11:08
 * 框架驱动类
 */
namespace core;
class Vivi{
    private static $classMap = array();
    public static function run(){
        //路由处理
        $run = new \core\lib\Route();
        
    }
    public static function load($class){
        if(in_array($class,self::$classMap)){
            return true;
        }else{
            //首先进行命名空间 和路径之间的斜杠之间的切换
            //\core\route

            $class = str_replace('\\','/',$class);
            //首字母大写
            $file = ucfirst(VIVI.'/'.$class.'.class.php');
            if(is_file($file)){
                self::$classMap[] = $class;
                include $file;
            }else{
                return false;
            }
        }
    }
}