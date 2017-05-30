<?php
//rand按照随机概率计算
function g_rand($max)
{
    $r_num = rand(1,100);
    if($r_num <= $max){
        return true;
    }else{
        return false;
    }
}

var_dump(g_rand(5));

//今天项目中需要用到一个简单的flag 但是一个用户可以有多个flag 所以这边用到位操作与异或来实现简单的falg

function inti($order)
{
    //给flag进行初始化
    $init = 0x1;
    return $init<<$order;
}

function comb($flag1,$flag2)
{
    //同时拥有两个标签的falg
    return $flag1|$flag2;
}

function check($targe,$flag)
{
    //1表示拥有这个标签  0表示没有该标签
    return $targe & $flag;
}
