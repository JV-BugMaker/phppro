<?php
//GENERATOR

function gen()
{
    $ret = (yield "yield1");
    var_dump($ret);
    $ret = (yield "yield2");
    var_dump($ret);
}

$gen = gen();
var_dump($gen->current());
var_dump($gen->send('ret1'));
var_dump($gen->send('ret2'));
//string(6) "yield1"
//string(4) "ret1"
//string(6) "yield2"
//string(4) "ret2"
//NULL
