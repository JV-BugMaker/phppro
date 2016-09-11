<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/11
 * Time: 下午10:37
 * 函数文件 定义常用到的函数
 */
function p($str){
    if(is_bool($str)){
        var_dump($str);
    }elseif (is_null($str)){
        var_dump(NULL);
    }else{
        echo "<pre style='position:relative;z-index: 1000;padding: 10px;border-radius: 5px;background: #F5F5F5;border: 1px solid #aaa;font-size:14px;line-height:18px;opacity: 0.9; '> ".print_r($str)."</pre>";
    }
}