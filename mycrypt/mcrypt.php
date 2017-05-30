<?php
//鸟哥博客中的 mcrypt扩展使用 坑

$dmcryptText = "dummy";
$key = "foobar";
$size = mcrypt_get_iv_size(MCRYPT_BLOWFISH,MCRYPT_MODE_ECB);

//这边采用默认的方式 也就是/dev/random
//按照鸟哥说法  我自己并不是很懂这块
//这里的问题就在于/dev/random, 它的random pool依赖于系统的中断来产生.
// 当系统的中断数不足, 不够产生足够的随机数, 那么尝试读取的进程就会等待, 也就是会hang住, 来看一个简单的例子:
$iv = mcrypt_create_iv($size);  //注意这里
//解决的办法就是, 改用/dev/urandom, /dev/urandom也是一个产生随机数的设备, 但是它不依赖于系统中断.
//mcrypt_create_iv($size, MCRYPT_DEV_URANDOM);
//但是为什么不直接默认这种模式 是因为这种模式的随机数比较容易被可预测性
//牛人 命令解决此问题 $ rngd -r /dev/urandom -o /dev/random -t 1
$m = mcrypt_ecb(MCRYPT_BLOWFISH, $key, $dmcryptText, MCRYPT_DECRYPT, $iv);
var_dump($m);
