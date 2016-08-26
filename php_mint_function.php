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

//调用一个程序, 程序退出-1, 但是PHP得到的为什么是255?

//    这个问题简单的说, 是因为exit或者main函数中的return, 只能使用0~255之间的值. -1 的unsigned值就是255.

//那么复杂点的说呢?

//    我们知道, 在Shell中, 运行一个命令, 一个程序, 都是fork一个子进程(然后exec)来执行的, 而这个程序的退出码, 被Shell(父进程),
// 通过wait来收集而后报告给我们的.

//而对于wait来说, 历史上原因, 他将通过statloc返回一个16bit的interge(现在也有用32位表示的, 但是会兼容已有的设计).
// 这16bits的interge中, 高8位就是程序退出的值(exit, 或者return), 而低八位表示导致这个程序退出的信号(其中一位表示是否有Core文件产生),
// 如果程序是正常退出, 那么低八位为0[1].
//11111111 00000000
//
//1	Catchall for general errors	let “var1 = 1/0″   	Miscellaneous errors, such as ”divide by zero” and other impermissible operations
//2	Misuse of shell builtins (according to Bash documentation)	empty_function() {}	    Seldom seen, usually defaults to exit code 1
//126	Command invoked cannot execute		    Permission problem or command is not an executable
//127	“command not found”	illegal_command	Possible problem with $PATH or a typo
//128	Invalid argument to exit	exit 3.14159	exit takes only integer args in the range 0 – 255 (see first footnote)
//128+n	Fatal error signal ”n”	kill -9 $PPID of script	$? returns 137 (128 + 9)
//130	Script terminated by Control-C		Control-C is fatal error signal 2, (130 = 128 + 2, see above)
//255*	Exit status out of range	exit -1	exit takes only integer args in the range 0 – 255



