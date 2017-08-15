<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 2017/8/4
 * Time: 11:33
 * Author: JV
 */

//预留 8192 字节
$emergency_mem_placeholder = str_repeat('1',5120);

register_shutdown_function(function(){
    if(! is_null($error = error_get_last())){
        //释放预留内存
        global $emergency_mem_placeholder;
        $emergency_mem_placeholder = 1;
    }
});