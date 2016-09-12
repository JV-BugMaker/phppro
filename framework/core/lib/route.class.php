<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/11
 * Time: 下午11:25
 */
namespace core\lib;

class Route
{
    public $controller;
    public $action;
    public function __construct()
    {
        //进行路由处理  首先需要去rewrite 隐藏掉index.php
        //然后是对 /index/index处理
        $path = $_SERVER['REQUEST_URI'];
        if(isset($path)&&$path!='/'){
            $path_arr = explode('/',trim($path,'/'));
            if(isset($path_arr[0])){
                $this->controller = $path_arr[0];
                unset($path_arr[0]);
            }else{
                $this->controller = 'index';
            }
            if(isset($path_arr[1])){
                $this->action = $path_arr[1];
                unset($path_arr[1]);
            }else{
                $this->action = 'index';
            }
            if($path_arr){
                //对多余参数的处理成GET方式
                $path_arr = array_values($path_arr);
                unset($_GET);
                for($i = 0,$len = count($path_arr);$i<$len;$i=$i+2){
                    if(!isset($path_arr[$i])){
                        continue;
                    }
                    $_GET[$path_arr[$i]] = $path_arr[$i + 1];
                }
            }
        }else{
            $this->controller = 'index';
            $this->action = 'index';
        }
    }
}
