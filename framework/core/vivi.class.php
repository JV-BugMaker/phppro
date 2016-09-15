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
    public static $controller;
    public static $action;
    public static $assign;
    public static function run(){
        //路由处理
        $run = new \core\lib\Route();
        //路由处理完成后需要对应相应的隐射表
        self::$controller = $ctrl = $run->controller;
        self::$action = $action = $run->action;
        $ctrlFile = APP.'/controller/'.ucfirst($run->controller).'Controller.php';
        if(is_file($ctrlFile)){
            //路由模块类 实例化之后 直接指向处理
            $module = '\\'.MODULE.'\\controller\\'.ucfirst($ctrl).'Controller';
            include $ctrlFile;
            $controller = new $module;
            $controller->$action();
        }else{
            throw new \Exception('404 not found');
        }

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

    public function assign($name,$value)
    {
        self::$assign[$name] = $value;
    }

    public function display()
    {
        $file = APP.'/view/'.ucfirst(self::$controller).'/'.self::$action.'.html';
        if(is_file($file)){
            extract(self::$assign);
            \Twig_Autoloader::register();
            $loader = new \Twig_Loader_Filesystem(APP.'/view/'.ucfirst(self::$controller));
            $twig = new \Twig_Environment($loader, array(
                'cache' => VIVI.'/cache/tmpcache',
                'debug'=> DEBUG
            ));
            $template = $twig->loadTemplate(self::$action.'.html');
            $template->display(self::$assign?self::$assign:'');
        }else{
            throw new \Exception('404 NOT FOUND');
        }
    }
}