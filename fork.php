<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/8/19
 * Time: 下午10:20
 */

$message_queue_key= ftok(__FILE__, 'a');
$message_queue= msg_get_queue($message_queue_key, 0666);

$pids= array();
for( $i = 0; $i<5; $i++) {
    $pids[$i] = pcntl_fork();
    if ($pids[$i]) {
        echo "No.$i child process was created, the pid is $pids[$i]\r\n";
    } elseif ($pids[$i] == 0) {
        $pid = posix_getpid();
        echo "process.$pid start\r\n";
        $count = 0;
        do {
            msg_receive($message_queue, 0,$message_type, 1024, $message, true, MSG_IPC_NOWAIT);
            echo "process. $pid deal message {$message}\r\n";
            $count++;
            if($count == 5) {
                break;
            }
            sleep(1);
        } while (true);
        echo "process. $pid end\r\n";
        posix_kill($pid, SIGTERM);
    }
}
for( $i = 0; $i<25; $i++) { msg_send($message_queue, 1,rand(1000,10000)); }