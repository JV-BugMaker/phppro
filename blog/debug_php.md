#PHP解决问题(By RANGO 程序员处理问题能力划分)

>rango 在博文中指出了程序员处理问题的8个能力级别 

* Lv0查看PHP错误信息

* Lv1存在多个版本的PHP或php-cli与php-fpm加载不同的配置问题

* Lv2var_dump()/die打印变量值信息单步调试 

* Lv3使用strace工具跟踪程序执行

* Lv4使用tcpdump工具分析网络通信过程

* Lv5统计函数调用的耗时和成功率

* Lv6gdb使用 

* Lv7查看PHP内核和扩展源码

##查看PHP错误信息

>线上一般可以tail -f php_errors.log日志文件。开发环境一般是使用xdebug来显示错误信息和定位。

>修改php.ini配置 

```
php.ini中display_errors / display_startup_errors设置为On
php.ini中error_reporting 设置为E_ALL
php在代码中设置error_reporting(E_ALL)
```
>php打开报错 

```
error_reporting(E_ALL);
ini_set('display_error','On');
```

##多个版本的php或php-cli与php-fpm加载不同的配置问题

```
which php
//得到 /usr/bin/php,查看php安装在哪

//推荐
php -i | grep php.ini
//Configuration File (php.ini) Path => /usr/local/etc/php/5.6
//Loaded Configuration File => /usr/local/etc/php/5.6/php.ini

//查看安装的扩展
php -m 
```

##单步调试

>PHP的getTrace调试程序

```
try{
    //问题程序段
}catch(Exception){
    var_dump($e->getTrace());
}

```

>php debug_backtrace()函数进行堆栈追踪
>使用var_dump()结合die()进行变量打印调试

##PHP进程追踪查看

>strace -- strace是一个非常简单的工具，它可以跟踪系统调用的执行。最简单的方式，它可以从头到尾跟踪binary的执行，然后以一行文本输出系统调用的名字，参数和返回值。 
>其实它可以做的更多： 
>可以对特定的系统调用或者几组系统调用进行过滤 
>可以通过统计特定系统调用的调用次数、耗费的时间、成功和失败的次数来配置(profile)系统调用的使用I 
>跟踪发送给进程的信号量 
>可以通过pid附着(attach)到任何运行的进程 
>如果你使用的是其它Unix系统，它类似于"truss"。其它更复杂的是Sun的Dtrace.

```
strace [ -dffhiqrtttTvxx ] [ -acolumn ] [ -eexpr ] …   
[ -ofile ] [ -ppid ] … [ -sstrsize ] [ -uusername ] [ command [ arg … ] ]  

strace -c [ -eexpr ] … [ -Ooverhead ] [ -Ssortby ] [ command [ arg … ] ]   
```

>跟踪程式执行时的系统调用和所接收的信号.通常的用法是strace执行一直到commande结束. 
>使用strace php test.php，或者strace -p 进程ID。strace就可以帮助你透过现象看本质，掌握程序执行的过程。
>找出程序在startup的时候读取的哪个config文件

```
$ strace php 2>&1 | grep php.ini  
open("/usr/local/bin/php.ini", O_RDONLY) = -1 ENOENT (No such file or directory)  
open("/usr/local/lib/php.ini", O_RDONLY) = 4  
lstat64("/usr/local/lib/php.ini", {st_mode=S_IFLNK|0777, st_size=27, ...}) = 0  
readlink("/usr/local/lib/php.ini", "/usr/local/Zend/etc/php.ini", 4096) = 27  
lstat64("/usr/local/Zend/etc/php.ini", {st_mode=S_IFREG|0664, st_size=40971, ...}) = 0  

strace -e open php 2>&1 | grep php.ini  
open("/usr/local/bin/php.ini", O_RDONLY) = -1 ENOENT (No such file or directory)  
open("/usr/local/lib/php.ini", O_RDONLY) = 4  

//监控进程情况
strace -p 15427  
Process 15427 attached - interrupt to quit  
futex(0x402f4900, FUTEX_WAIT, 2, NULL   
Process 15427 detached  

//查看CPU花费时间在哪
strace -c -p 11084  
Process 11084 attached - interrupt to quit  
Process 11084 detached  
% time     seconds  usecs/call     calls    errors syscall  
------ ----------- ----------- --------- --------- ----------------  
 94.59    0.001014          48        21           select  
  2.89    0.000031           1        21           getppid  
  2.52    0.000027           1        21           time  
------ ----------- ----------- --------- --------- ----------------  
100.00    0.001072                    63           total  
```

##通信过程中如何分析 tcpdump

>默认启动方式，将监视第一个网络接口上流过的数据包
 
```
tcpdump
```

>监视指定网络接口的数据包s

```
tcpdump -i eth1
```

>监视指定主机的数据包

```
//打印所有进入或者离开sundown的数据包
tcpdump host sundown/ip

也可以指定ip,例如截获所有210.27.48.1 的主机收到的和发出的所有的数据包

tcpdump host 210.27.48.1 
打印helios 与 hot 或者与 ace 之间通信的数据包

tcpdump host helios and \( hot or ace \)
截获主机210.27.48.1 和主机210.27.48.2 或210.27.48.3的通信

tcpdump host 210.27.48.1 and \ (210.27.48.2 or 210.27.48.3 \) 
打印ace与任何其他主机之间通信的IP 数据包, 但不包括与helios之间的数据包.

tcpdump ip host ace and not helios
如果想要获取主机210.27.48.1除了和主机210.27.48.2之外所有主机通信的ip包，使用命令：

tcpdump ip host 210.27.48.1 and ! 210.27.48.2
截获主机hostname发送的所有数据

tcpdump -i eth0 src host hostname
监视所有送到主机hostname的数据包

tcpdump -i eth0 dst host hostname
```

>监视指定主机和端口的数据包

```
如果想要获取主机210.27.48.1接收或发出的telnet包，使用如下命令

tcpdump tcp port 23 and host 210.27.48.1
对本机的udp 123 端口进行监视 123 为ntp的服务端口

tcpdump udp port 123 
```

>监视指定协议的数据包

```
打印TCP会话中的的开始和结束数据包, 并且数据包的源或目的不是本地网络上的主机.(nt: localnet, 实际使用时要真正替换成本地网络的名字))

tcpdump 'tcp[tcpflags] & (tcp-syn|tcp-fin) != 0 and not src and dst net localnet'
打印所有源或目的端口是80, 网络层协议为IPv4, 并且含有数据,而不是SYN,FIN以及ACK-only等不含数据的数据包.(ipv6的版本的表达式可做练习)

tcpdump 'tcp port 80 and (((ip[2:2] - ((ip[0]&0xf)<<2)) - ((tcp[12]&0xf0)>>2)) != 0)'
(nt: 可理解为, ip[2:2]表示整个ip数据包的长度, (ip[0]&0xf)<<2)表示ip数据包包头的长度(ip[0]&0xf代表包中的IHL域, 而此域的单位为32bit, 要换算

成字节数需要乘以4,　即左移2.　(tcp[12]&0xf0)>>4 表示tcp头的长度, 此域的单位也是32bit,　换算成比特数为 ((tcp[12]&0xf0) >> 4)　<<　２,　
即 ((tcp[12]&0xf0)>>2).　((ip[2:2] - ((ip[0]&0xf)<<2)) - ((tcp[12]&0xf0)>>2)) != 0　表示: 整个ip数据包的长度减去ip头的长度,再减去
tcp头的长度不为0, 这就意味着, ip数据包中确实是有数据.对于ipv6版本只需考虑ipv6头中的'Payload Length' 与 'tcp头的长度'的差值, 并且其中表达方式'ip[]'需换成'ip6[]'.)

打印长度超过576字节, 并且网关地址是snup的IP数据包

tcpdump 'gateway snup and ip[2:2] > 576'
打印所有IP层广播或多播的数据包， 但不是物理以太网层的广播或多播数据报

tcpdump 'ether[0] & 1 = 0 and ip[16] >= 224'
打印除'echo request'或者'echo reply'类型以外的ICMP数据包( 比如,需要打印所有非ping 程序产生的数据包时可用到此表达式 .
(nt: 'echo reuqest' 与 'echo reply' 这两种类型的ICMP数据包通常由ping程序产生))

tcpdump 'icmp[icmptype] != icmp-echo and icmp[icmptype] != icmp-echoreply'
```

>使用tcpdump抓取HTTP包

```
tcpdump  -XvvennSs 0 -i eth0 tcp[20:2]=0x4745 or tcp[20:2]=0x4854
//0x4745 为"GET"前两个字母"GE",0x4854 为"HTTP"前两个字母"HT"。
tcpdump 对截获的数据并没有进行彻底解码，数据包内的大部分内容是使用十六进制的形式直接打印输出的。显然这不利于分析网络故障，通常的解决办法是先使用带-w参数的tcpdump 截获数据并保存到文件中，然后再使用其他程序(如Wireshark)进行解码分析。当然也应该定义过滤规则，以避免捕获的数据包填满整个硬盘。
```

>一些详细的协议内容方面的东西，我也不是很清楚。可以参考[tcpdump](http://www.cnblogs.com/ggjucheng/archive/2012/01/14/2322659.html)


##使用统计函数统计耗时和成功率

>microtime(true)--使用毫秒级别统计从接口返回数据的统计，成功率计算等。考验程序员对接口反应的敏感程度

>其他的方式gdb以及查看内核源码什么的我不是很清楚诶。推荐一个github地址[Linux工具快速教程](http://linuxtools-rst.readthedocs.io/zh_CN/latest/)
