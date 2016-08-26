<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/8/25
 * Time: 上午12:51
 */
//当Apache启动的时候，主进程会首先把所有模块load进来，然后又卸载掉 他们，这是第一遍，目的是为了检查配置文件。
//如果一切正常，Apache就会重新 load一便所有的模块，然后才fork子进程，这样，PHP模块就会只被初始化一次， 然后在fork的时候复制给子进程。
//当apache关闭的时候 ，每个自进程都会去调用一次模块的关闭函数。

//也就是说， PHP模块的初始化函数只会被调用一次，而关闭函数会被调用多次。