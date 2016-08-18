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


/*function getStatus($arg){
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
*/

//PHP的sysvmsg模块是对Linux系统支持的System V IPC中的System V消息队列函数族的封装。
$mesg_key = ftok(__FILE__, 'a');
//key 就是用来进程之间的通行的key  0666 是一个默认值 返回一个资源类型
$mesg_id = msg_get_queue($mesg_key, 0666);
//会根据传入的键值返回一个消息队列的引用。 如果linux中没有消息对应这个队列,此函数将会创造一个新的队列。
//函数的第二个参数需要传入一个int值，作为新创建的消息队列的权限值，默认为0666。这个权限与chmod是一个概念
//linux中一切皆文件
function fetchMessage($mesg_id){
    //判断是否是资源类型进程key
    if(!is_resource($mesg_id)){

        print_r("Mesg Queue is not Ready");

    }
    //用于读取消息队列中的数据  与send发送的需要一致
    if(msg_receive($mesg_id, 0, $mesg_type, 1024, $mesg, false, MSG_IPC_NOWAIT)){
        //会自带 s19 s20? 什么问题
        //struct msgbuf{
        //long mtype;
        //char mtext[1];
        //};
        print_r("Process got a new incoming MSG: $mesg \n");

    }

}

register_tick_function("fetchMessage", $mesg_id);

declare(ticks=2){

    $i = 0;

    while(++$i < 100){

        if($i%5 == 0){
            //向对应key 的队列中写写入消息 进行发送  key type message
            msg_send($mesg_id, 1, "Hi: Now Index is :". $i);
        }
    }
}
//msg_stat_queue ( resource $queue )
//这个函数会返回消息队列的元数据。消息队列元数据中的信息很完整，包括了消息队列中待读取的消息数、最后读写队列的进程ID等。


//信号量的概念，大家应该都很熟悉。通过信号量，可以实现进程通信，竞争等。 再次就不赘述了，只是简单的列出PHP中提供的信号量函数集。
//sem_acquire -- Acquire a semaphore
//sem_get -- Get a semaphore id
//sem_release -- Release a semaphore
//sem_remove -- Remove a semaphore
/*
 * 使用shmop系列函数 实现内存共享
 *
 * */
function memoryUsage(){
    printf("%s: %s<br/>", date("H:i:s", time()), memory_get_usage());

    //var_dump(debug_backtrace());

    //var_dump(__FUNCTION__);

    //debug_print_backtrace();

}

register_tick_function("memoryUsage");

declare(ticks=1){

        $shm_key = ftok(__FILE__, 's');
        //打开或创建一个内存块来共享
        $shm_id = shmop_open($shm_key, 'c', 0644, 100);

}

printf("Size of Shared Memory is: %s<br/>", shmop_size($shm_id));
//读取共享的内存块
$shm_text = shmop_read($shm_id, 0, 100);
//eval() 函数把字符串按照 PHP 代码来计算。
//　　该字符串必须是合法的 PHP 代码，且必须以分号结尾。
//　　如果没有在代码字符串中调用 return 语句，则返回 NULL。如果代码中存在解析错误，则 eval() 函数返回 false。
eval($shm_text);

if(!empty($share_array)){

    var_dump($share_array);

    $share_array['id'] += 1;

}else{

    $share_array = array('id' => 1);

}

$out_put_str = "$share_array = " . var_export($share_array, true) .";";
//填充字符串 在右边 填充到长度为100
$out_put_str = str_pad($out_put_str, 100, " ", STR_PAD_RIGHT);
//写入到共享内存
shmop_write($shm_id, $out_put_str, 0);
