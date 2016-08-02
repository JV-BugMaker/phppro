<?php
//GENERATOR

function gen()
{
    $ret = (yield "yield1");
    var_dump($ret);
    $ret = (yield "yield2");
    var_dump($ret);
}
//gen() 函数是实现一个迭代器
$gen = gen();
//重点来了
//首先是执行$gen->current()   碰到第一个yield gen执行终止 需要等待往下执行的命令 返回值也就是yield1
//之后执行的就是var_dump($gen->send('ret1')) 此时收到继续往下执行的命令 但是此时 yield并不是作为终止命令 因为send是带参数过来
//yield是作为表达式存在 即$ret = 'ret1';
//作为表达式的时候 并不需要终止执行 就继续往下执行
//碰到 yield2 之后  终止执行了 返回值是yield2
//此时跳出gen函数 执行 var_dump($gen->send('ret2'));
//执行send时带参数ret2 执行gen中的var_dump()
//最后一个NULL 其实就是gen执行完毕之后没有返回值  $gen->send('ret2') == NULL
//执行send的时候其实是隐士执行了next函数
//纯属个人理解
var_dump($gen->current()); //第一个yield时候 跳出函数
var_dump($gen->send('ret1'));
var_dump($gen->send('ret2'));
//string(6) "yield1"
//string(4) "ret1"
//string(6) "yield2"
//string(4) "ret2"
//NULL
