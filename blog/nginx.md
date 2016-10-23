#Nginx高性能Web服务器详解笔记

##第二章

###基于IP进行虚拟主机配置

>Linux下为网卡添加别名

```
ifconfig eth0:0 192.168.1.31 netmask 255.255.255.0 up 

```
up表示立即启用

Linux系统的启动脚本是rc.local

```
echo "ifconfig eth0:1 192.168.1.31 netmask 255.255.255.0 up" >> /etc/rc.local
```

在server_name中匹配响应的IP地址。

###配置location块

```
location [= | ~ |~*|^~] uri{...}
```

>"=",用于标准uri之前，要求请求字符串与uri严格匹配，如果匹配中成功就停止继续向下搜索并立志处理该请求。

>“~”，用于表示uri包含正则表达式，并且区分大小写。

>“~*”，用于表示uri包含正则表达式，并且不区分大小写。

如果uri包含正则表达式，就必须使用“~”或者“~*”

>“^~”,用于标准uri前，要求nginx服务器找到标识uri和请求字符串匹配度最高的location后，立即使用此location处理请求，而不在使用location块中的正则uri和请求字符串做匹配中。

注：浏览器传送的uri时对部分字符串进行url进行编码，“^~”，它对uri中的这些编码处理。


###更改location的uri

```
alias path
```
eg：

```
location ~ ^/data/(.+\.(html|htm))$
{
	alias /local/other/$1;	
}
```
请求/data/index.html将会把请求转到/local/other 目录里面。

###设置网站的默认首页

```
location ~ ^/data/(.+)/web/ $
{
	index index.$1.html index.my1.html index.html;
}
$1 表示location中的正则表达式匹配部分 /data/site/web  $1 ---- site
```

###设置nginx服务器网站错误页面

```
error_page code ... [=[response]] uri
```
>'code',要处理的HTTP错误代码。

>‘response’，可选项，将code制定的错误代码转化为新的错误代码

>‘uri’，错误页面的路径或者网站地址。定向到需要的地址

```
error_page 404 /404.html
error_page 410 =301 /empty.gif
```

设置完error错误页面之后，可以设置nginx，location块来处理下一步。

```
location /404.html
{
	root /myserver/errorpage;
}
```
error_page指令可以在http块、server块和location块中配置。

###基于IP配置Nginx的访问权限

```
deny|allow address | CIDR | all;
```
>'address', 允许访问客户端的IP，不支持同时设置多个。

>‘CIDR’，允许访问的客户端的CIDR地址，例如202.80.18.23/25，前面部分是32位IP地址，后面部分代表该IP中前25位是网络部分。

>‘all’，表示允许所有客户端访问。

```
location /
{
	deny 192.168.1.1;
	allow 192.168.0.1/25;
	deny allow;
}
在此规则中，只要有相对应的匹配项的时候就会停止搜索。

```
###基于密码配置Nginx的访问权限

该功能标准模块ngx_auth_basic_module

```
auth_basic string | off;
```
>'string', 开启该认证功能，并配置验证时的指示信息。

>‘off’，关闭该认证功能。

auth_basic_user_file 指令，用于设置包含用户名和密码信息的文件路径。

```
auth_basic_user_file file;
```
>'file',为密码文件的绝对路径。

```
name:password 
```
如何生存密码文件

```
htpasswd -c -d /path username  #然后输入密码即可
```

##Nginx服务器架构初探

Nginx涉及到的模块分为核心模块、标准HTTP模块、可选HTTP模块、邮件模块以及第三方模块等五大类。

###核心模块

* 主体功能：进程管理、权限控制、错误日志记录、配置解析等。
* 响应请求事件必须的功能：事件驱动机制、正则表达式解析等。

###标准http模块

|模块 | 功能 | 
| -----|:----:|
| ngx\_http\_core    | 配置端口、URI分析、服务器响应错误处理、别名控制以及其他http核心失误    |
| ngx\_http\_access\_module    | 基于IP地址的访问控制（允许/拒绝）    |
| ngx\_http\_auth\_basic\_module    | 基于HTTP的身份认证    |
| ngx\_http\_autoindex\_module | 处理以‘/’结尾的请求并自动目录 |
| ngx\_http\_browser\_module | 解析HTTP请求头中的"User-Agent"域的值 |
| ngx\_http\_charset\_module | 指定网页编码 |
| ngx\_empty\_gif\_module | 从内存中创建一个1 * 1的透明gif图片，可以快速调用 |
| ngx\_http\_fastcgi\_module |对fastcgi的支持|
| ngx\_http\_geo\_module | 将客户端请求中的参数转化为键值对变量 |
| ngx\_http\_gzip\_module | 压缩请求响应，可以减少数据传输 |
| ngx\_http\_headers\_filter\_module | 设置HTTP响应头 |
| ngx\_http\_index\_module | 处理以‘/’结尾的请求，如果没有找到该目录下的index页，就会将请求转给ngx\_http\_autoindex\_module模块处理；|
| ngx\_http\_limit\_req\_module | 限制来自客户端的请求的响应和处理速率 |
| ngx\_http\_limit\_conn\_module | 限制来自客户端的连接的响应和处理速率 |
| ngx\_http\_log\_module | 自定义access日志 |
| ngx\_http\_map\_module | 创建任意键值对变量 |
| ngx\_http\_memcached\_module | 对memcached的支持 |
| ngx\_http\_proxy\_module | 支持代理服务 |
| ngx\_http\_referer\_module | 过滤HTTP头中‘Referer’域值为空的‘http’请求 |
| ngx\_http\_rewrite\_module | 通过正则表达式重定向| 
| ngx\_http\_scgi\_module | 对scgi的支持 |
| ngx\_http\_ssl\_module | 对https的支持 |
| ngx\_http\_upstream\_module | 定义一组服务器，可以接收来自代理、fastcgi、memcached的重定向，主要用于负载均衡|

###可选http模块

|模块 | 功能 | 
| -----|:----:|
| ngx\_addition\_module    | 在响应请求的页面开始或者结尾添加文本信息 |
| ngx\_http\_degradation\_module |在低内存的情形下允许nginx服务器返回404错误或者204错误 |
| ngx\_http\_perl\_module | 在nginx的配置文件中可以使用perl脚本 |
| ngx\_http\_flv\_module | 支持将flash多媒体信息按照流文件传输，可以根据客户端指定的开始位置返回flash |
| ngx\_http\_geoip\_module | 支持解析基于geoip数据库的客户端请求 |
| ngx\_google\_perftools\_module | 支持google performance tools |
| ngx\_http\_gzip\_module | 支持在线实时压缩响应客户端的输出数据流 |
| ngx\_http\_gzip\_static\_module | 搜索并使用预压缩的以‘.gz’为后缀名的文件替代一般文件响应客户端请求 |
| ngx\_http\_image\_filter\_module | 支持改变jpeg、gif、和png图片的尺寸和旋转方向 |
| ngx\_http\_mp4\_module | 支持将H.264/AAC编码的多媒体信息 |
| ngx\_http\_random\_index\_module | nginx接收到以‘/’结尾的请求时，在对应的目录下随机选择一个文件作为index文件。|
| ngx\_http\_secure\_link\_module | 支持对请求链接的有效性检查 |
| ngx\_http\_ssl\_module | 对https/ssl的支持 |
| ngx\_http\_stub\_status\_module | 支持返回nginx服务器的同济信息，一般包括处理连接数量、连接成功的数量、处理的请求数、读取和返回的header信息数等信息 |
| ngx\_http\_sub\_module | 使用指定的字符串替换响应信息中的信息 |
| ngx\_http\_dev\_module | 支持http协议和webDAV协议中PUT、DELETE、MKCOL、COPY和MOVE方法 |
| ngx\_http\_xslt\_module | 将XML响应信息使用XSLT（扩展样式表转换语言）进行转换 |

###nginx服务器的web请求处理机制

完成并行处理请求工作有三种方式可供选择：多进程方式、多线程方式和异步方式

同步与异步方式-- 我个人理解就是接收方这边的处理的方式 。

阻塞与非阻塞 -- 个人理解就是发送方发送请求之后是否等待响应的方式。

###Nginx服务器如何处理请求

Nginx服务器的进程模式有两种：Single和master-worker模型。Single模型为单进程方式，性能较差。

###Nginx服务器的事件驱动模型

事件驱动模型一般是由事件收集器、事件发送器和事件处理器三部分基本单元组成。
事件驱动机制实现方式有多种，主要是批次程序设计。

使用批次程序设计，其流程是由程序设计师在设计编码过程中决定的，也就是说，在程序运行的过程中，事件的发生、事件的发送和事件的处理都是预先设计好的。

事件驱动模型中的事件发生器三种处理方式:

* 传递过来一个请求，就创建新的进程。创建新的进程开销比较大，会导致服务器性能较差，但是实现方式比较简单。
* 传递过来一个请求，就穿件新的线程。涉及到线程同步问题，可能会面临死锁、同步问题等一系列问题，代码表复杂。
* 传递过来一个请求，将其放入一个待处理事件的列表，使用非阻塞IO方式调用。

事件驱动处理库又被称为多路IO复用方法，最常见三种就是：select模型、poll模型和epoll模型。同时nginx也支持rtsig模型、kqueue模型、dev/poll模型和eventport模型。

###select库

首先创建所关注事件的描述符集合。对于一个描述符，可以关注其上面的读事件、写事件和异常事件。

其次是调用底层提供的select()函数，等待事件发生。select的阻塞与是否设置非阻塞IO是没有关系的。

然后轮询所有事件描述符集合中的每一个事件描述符，检查是否有相应的事件发生，如果有，就进行处理。

```
--with-select_module 或者  --without-select_module 在编译的时候指定
```


###poll库

poll库与select库的基本工作方式是相同的，都是先创建一个关注事件的描述符集合，再去等待这些事件发生，然后轮询描述符集合，检查有没有事件发生。

poll库可以认为是select库的优化版本，其主要区别就是select库需要为读事件、写事件和异常事件分别创建一个描述符集合，因此在轮询的时候，需要分别轮询这三个集合。而poll库值需要创建一个集合，描述符对应的结构上分别设置读事件、写事件或者异常事件，最后轮询的时候，可以同时检查这sane事件是否发生。

```
--with-poll_module # --without-poll_module 
```

###epoll库

epoll库是nginx服务器支持的高性能事件驱动库之一。

首先，epoll库通过相关调用通知内核创建一个有N个描述符的事件列表；然后，给这些描述符设置所关注的事件，并把它添加到内核的事件列表中去。

完成设置，epoll库就开始等待内核通知事件发生。某一事件发生后，内核将发生事件的描述符列表上报给epoll库，epoll就进行事件处理。

epoll库的IO效率不随描述符数目增加而线性下降，因为它只会对内核上报的“活跃”的描述符进行操作。

###rtsig模型

rtsig是real-time signal的缩写。使用rtsig模型时，工作进程会通过系统内核建立一个resig队列用于存放标记事件发生的信号。每个事件发生时，系统内核就会产生一个信号存放到rtsig队列中等待工作进程的处理。


###其他事件模型

kqueue模型也是poll模型的一个变种，都是避免轮询操作提供效率。用于支持BSD系列平台，OPENBSD2.9，NETBSD2.0 。该模型同事支持条件出发（水平出发）和边缘触发（状态改变时触发一个事件）。


/dev/poll模型，使用了虚拟的/dev/poll设备，开发人员可以将要监视的文件描述符加入这个设备，然后通过ioctl()调用来获取事件通知。

eventport模型，实现机制类似/dev/poll模型，它可以有效防止内核崩溃等情况发生。

以上各种模型都是基于不同的运行平台进行优化开大的，所以在选在nginx运行的事件驱动模型的时候需要考虑平台本身性质。


###Nginx服务器的进程

Nginx服务器的三大类进程：一类是主进程，二类是是由主进程生成的工作进程，三类是为了缓存文件建立索引的进程。

1.主进程：Nginx服务器启动时运行的主要进程。

* 读取Nginx配置文件并验证其有效性和正确性。
* 建立、绑定和关闭socket。
* 按照配置生成、管理和结束工作进程。
* 接收外接指令，比如重启、升级及退出服务器等指令。
* 不中断服务器，实现平滑重启，应用新配置。
* 不中断服务器，实现平滑升级，升级失败进行回滚处理。
* 开启日志文件，获取文件描述符。
* 编译和处理perl脚本。

2.工作进程：由主进程生成。

* 接收客户端请求。
* 将请求依次送入各个功能模块进行过滤处理。
* IO调用，获取响应数据。
* 与后端服务器通信，接收后端服务器处理结果。
* 数据缓存，访问缓存索引、查询和调用缓存数据。
* 发送请求结果，响应客户端请求。
* 接收主进程指令，比如重启、升级和退出等指令。

3.缓存索引重建及进程管理：缓存索引重建进程是在Nginx服务启动一段时间后（默认1分钟）由主进程生成，在缓存元数据重建完成后就自动退出。

>缓存索引重建进程完成的主要工作是，根据本地磁盘上的缓存文件在内存中建立元数据库。该进程启动后，对本地磁盘上缓存文件的目录结构进行扫描，检查内存中已有的缓存元数据是否正确，并更新索引元数据库。

###进程交互

1.master-worker交互

在主进程生成工作进程后，将新生成的工作进程加入到工作进程表中，并建立一个单向管道并将其传递给该工作进程。它是由主进程指向工作进程的单向管道，包含了主进程向工作进程发出的指令、工作进程ID、工作进程在工作进程表中的索引和必要的文件描述符等信息。

主进程与外界通过信号机制进行通信，当接收到需要处理的信号时，它通过管道向相关的工作进程发送正确的指令。每个工作进程都有能力捕获管道中可读时间，当管道中有可读事件时，工作进程从管道读取并解析指令，然后才去相应的措施。

2.worker-worker交互

当工作进程W1需要向W2发送指令时，首先在主进程给它的其他工作进程信息中找到W2的进程ID，然后将正确的指令写入指向W2的管道。工作进程W2捕获到管道中的事件后，解析指令并采取相应措施。


##第四章Nginx高级配置

