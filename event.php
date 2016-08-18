<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/8/17
 * Time: 下午11:16
 */
//信号量(Semaphores)
//共享内存(Shared Memory)
//进程间通信(Inter-Process Messaging, IPC)  他们是对UNIX的V IPC函数族的包装。

//ftok($path,$proj); //ftok是将一个路径名为path和一个项目名(必须是字符串) 转化成一个整形的用来的使用系统V IPC的key
//Ticks是从PHP 4.0.3开始才加入到PHP中的，它是一个在declare代码段中解释器每执行N条低级语句就会发生的事件。
//N的值是在declare中的directive部分用ticks=N来指定的。


function getStatus($arg){
    print_r(connection_status());

    debug_print_backtrace();

}
//注册declar事件
register_tick_function("getStatus", true);


declare(ticks=1){

    for($i =1; $i<5; $i++){

        echo "hello";

    }

}

unregister_tick_function("getStatus");

$mesg_key = ftok(__FILE__, 'm');
$mesg_id = msg_get_queue($mesg_key, 0666);

function fetchMessage($mesg_id){

    if(!is_resource($mesg_id)){

        print_r("Mesg Queue is not Ready");

    }

    if(msg_receive($mesg_id, 0, $mesg_type, 1024, $mesg, false, MSG_IPC_NOWAIT)){

        print_r("Process got a new incoming MSG: $mesg ");

    }

}

register_tick_function("fetchMessage", $mesg_id);

declare(ticks=2){

    $i = 0;

    while(++$i < 100){

        if($i%5 == 0){

            msg_send($mesg_id, 1, "Hi: Now Index is :". $i);
        }
    }
}

