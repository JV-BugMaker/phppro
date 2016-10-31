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

* 针对IPv4的内核参数优化
* 针对处理器的指令配置
* 针对网络连接的指令配置
* 与实践驱动相关的指令配置

###针对IPv4的内核参数7个参数的配置优化

这边涉及的参数是和IPv4网络有关的linux内核参数，在系统文件中使用一下命令修改生效

```
#/sbin/sysctl -p
```

1.net.core.netdev_max_backlog参数

表示每个网络接口接收数据包的速率比内核处理这些包的速率快时，允许发送到队列的数据包的最大数目。一般默认是128，NGINX服务器中定义的NGX_LISTEN_BACKLOG默认是511。
```
net.core.netdev_max_backlog = 262144
```

2.net.core.somaxconn参数

该参数用于调节系统同事发起tcp连接数，一般认为是128。在客户端存在高并发请求的情况下，该默认值比较小，可能导致连接超时或者重传问题。

3.net.ipv4.tcp_max_orphans参数

该参数用于设定系统中最多允许存在多少tcp套接字不被关联到任何一个用户文件句柄上。一旦超过这个数字，没有与用户文件句柄关联的tcp套接字将立即被复位，给出警告信息。这个是为了方式DoS攻击。

4.net.ipv4.tcp_max_syn_backlog参数

该参数用于记录尚未收到客户端确认信息的连接请求的最大值。

5.net.ipv4.tcp_timestamps参数

该参数用于设置时间戳，这可以避免序列号的卷绕。在一个1Gb/s的链路上，遇到以前用过的序列号的概率很大。当此值赋值为0时，禁用对于tcp时间戳的支持。

6.net.ipv4.tcp_synack_retries参数

该参数用于设置内核放弃tcp连接之前向客户端发送syn+ack包数量。为了建立对端的连接服务，服务器和客户端需要进行三次握手，第二次握手期间，内核需要发送syn并附带一个回应前一个syn的ack，这个参数主要影响这个过程，一般赋值为1，即内核放弃连接之前发送一次syn+ack包。

7.net.ipv4.tcp_syn_retries

该参数的作用与上一个参数类似，设置内核放弃建立连接之前发送syn包的数量。

###与网络连接相关的配置的4个指令

1. keepalive_timeout 设置nginx服务器与客户端保持连接的超时时间
1. send_timeout 设置nginx服务器响应客户端超时时间
1.	client_header_buffer_size nginx服务器允许请求头部的缓冲区大小 #此指令可以根据系统分页大小来设置 

```
getconf PAGESIZE
```
>当nginx服务器返回400错误的情况时，可以考虑是否是客户端请求头部过大造成的。

###与事件驱动模型相关的配置的8个指令

* use use用于指定nginx服务器使用事件驱动模型。
* worker_connection 用于设置nginx服务器的每个工作进程允许同时连接客户端的最大数量
client = worker_process * worker_connections / 2 此指令一般情况下设置为65535
,此指令的赋值与linux操作系统中进程可以打开的文件句柄数量有关。

```
cat /proc/sys/fs/file-max 
echo "2390251" > /proc/sys/fs/file-max; sysctl -p
```

* worker_rlimit_sigpending  该指令用于设置linux平台的事件信号队列长度，基于rtsig模型的最大信号数。
* devpoll_changes 和 devpoll_events 设置/dev/poll事件驱动模型下nginx服务器可以与内核之间传递事件的数量
* kqueue_changes 和 kqueue_events 设置kqueue模型下nginx服务器可以与内核之间传递事件的数量。changes是设置传递给内核的事件数，events是从内核获取事件数量。
* epoll_events 用于设置epoll事件驱动模型下nginx服务器可以与内核之间传递事件的数量。
* rtsig_signo 用于设置rtsig模型使用的两个信号编号间隔。
* rtsig_overflow_* events（指定队列溢出时使用poll库处理的事件数，16）、test（指定poll库处理完第几件事件之后将清空rtsig模型使用的信号队列，默认32）、threshold（指定rtsig模式使用的信号队列中的事件超过多少时就需要清空队列了，10）指令。用来控制rtsig模式中信号队列溢出时nginx服务器的处理方式。

##Nginx服务器的Gzip压缩

>在nginx配置文件中配置gzip的使用，指令在配置文件http块、server块、location块中设置，nginx服务器通过ngx\_http\_gzip\_module模块、ngx\_http\_gzip\_static\_module块和ngx\_http\_gunzip\_module模块。

###由ngx\_http\_gzip\_module模块处理的9个指令

该模块主要负责gzip功能的开启和设置，对响应数据进行实时压缩。

1.gzip指令

```
gzip on | off;
```

2.gzip\_buffers

该指令用于设置gzip压缩文件使用缓存空间的大小

```
gzip_buffers number size;
number 指定nginx服务器需要向系统申请缓存空间的个数
size 指定每个缓存空间的大小
```

gzip压缩时需要向系统申请number * size大小的空间进行数据压缩。默认情况下number*size的值为128，其中size取内存页一页的大小为4kb或者8kb

```
gzip buffers 32 4k| 16 8k;
```

3.gzip\_comp\_level指令

指定用于设定gzip压缩程度，从1到0.级别1表示压缩程度最低，效率最高。

4.gzip\_disable指令

针对不同种类客户端发起的请求，可以选择性的开启和关闭gzip功能。

```
gzip_disable regex ...;
```

regex 根据客户端的浏览器标识UA进行设置，支持使用正则表达式。

5.gzip\_http\_version指令

针对不同的http协议版本，需要选择性地开启或者关闭gzip功能。该指令用于设置gzip功能的最低http版本。

```
gzip_http_version 1.0|1.1;
```
6.gzip\_min\_length指令

gzip压缩功能对大数据的压缩效果越明显，但是如果压缩很小的数据，可能会出现越压缩数据量越大的情况。
 
该指令设置页面的字节数，当响应页面的大小大于该值时才会开启gzip功能。大小是从http响应头部中content_length指令。但是如果使用chunk编码动态压缩content_length活不存在或被忽略。

```
gzip_min_length length;
```

7.gzip\_proxied指令

该指令在使用nginx服务器的反向代理功能时有效，前提是在后端服务器返回的响应页头部中，request部分包含用于通知代理服务器的via头域。

```
gzip_proxied off | expired | no-cache | no-store | private | no_last_modified | no_etag | auth | any ...;
#off 关闭nginx服务器对后端服务器返回结果的gzip压缩，默认设置。
#expired 当后端服务器响应页头部包含用于指示响应数据过期时间的expired头域，启用对响应数据的gzip压缩。
#no-cache 当后端服务器响应头部包含用于通知所有缓存机制是否缓存的cache-control头域、且其指令值为no-cache时，启用对响应数据的gzip压缩。
#no-stre 当后端服务器响应头部包含用于通知所有缓存机制是否缓存的cache-control头域、且其指令值为no-store时，启用对响应数据的gzip压缩。
#private 当后端服务器响应头部包含用于通知所有缓存机制是否缓存的cache-control头域、且其指令值为private时，启用对响应数据的gzip压缩。
#no_last_modify 当后端服务器响应头页不包含用于指明需要获取数据最后修改时间的last_modify头域时，启用对响应数据的gzip压缩。
#no_etag 当侯丹服务器响应页头部不包含用于标示被请求变量的尸体值的etag头域，启用对响应数据的gzip压缩。
#auth 当后端服务器响应页头部包含用于标示http授权证书的authorization头域时，启用对响应数据的gzip压缩。
#any 无条件启用对后端服务器响应数据的gzip压缩。
```
8.gzip\_types指令

nginx服务器可以根据响应页的MIME类型选择性的开启gzip压缩功能。

```
gzip_types mime-type ...;
gzip_types text/plain application/x-javascript text/css text/html application/xml ;
```

9.gzip_vary 指令

该指令用于设置在使用gzip功能时是否发送带有"Vary:Accept-Encoding"头域响应头部。告诉接收方发送的数据经过了压缩处理。

```
gzip_vary on |off;
```

###由ngx\_http\_gzip\_static\_module模块处理的指令

>ngx\_http\_gzip\_static\_module模块主要负责搜索和发送经过gzip功能压缩的数据。数据以".gz"作为后缀名存储在服务器上，如果数据已经被压缩了，直接返回该数据。该模块使用的是静态压缩。该模块相关的指令：gzip\_static gzip\_version gzip\_proxied gzip\_disable 和gzip\_vary 等。

```
gzip_static on | off |always;
```
其他指令都是如同上一节所说。

###由ngx\_http\_gunzip\_module模块处理的2个指令

该模块是用来处理针对不支持gzip压缩数据处理的客户端浏览器，对压缩数据进行解压处理的，与它相关的指令有：gunzip、gunzip\_buffers gzip\_http\_version gzip\_proxied gzip\_disable和gzip\_vary 

1.gunzip指令

该指令用于开启或者关闭该模块功能。

```
gunzip_static on |off;
```

2.gunzip_buffers指令

用于设置nginx服务器解压gzip文件使用缓存空间的大小。

```
gunzip_buffers number size ;
```


3.nginx使用gzip综合实例

在http块中的配置

```
gzip on;
gzip_min_length 1024;
gzip_buffers 4 16k;
gzip_comp_level 2;
gzip_types text/plain application/x-javascript text/css application/xml;
gzip_vary on;
gunzip_static on;
```

###nginx服务器gzip模块与浏览器不兼容处理

```
<!--[if ie 6.0 ]>
<script src="index.js" type="text/c"></script>
<![endif]-->
#其中text\c保证脚本加载但不运行，从而不会对客户端性能造成影响。
```
增加gzip_disable "MSIE[1-6]\.";

###nginx服务器与其他服务器交互时产生gzip压缩功能相关问题

一类是多层服务器同时开启gzip压缩功能，二类是多层服务器之间对gzip压缩功能支持能力不同。

如果前后端服务器同时开启gzip模块对JavaScript脚本进行压缩，页面加载时候JavaScript能够正常运行。但是当页面重新刷新，即304状态，并没有修改的情况下就是会出现执行错误。对于这样的情况，一般就是nginx服务器开启压缩，后端不进行压缩。

第二种情况下squid服务器就是能静态压缩的情况下，nginx服务器就用开启静态压缩来使数据进行压缩，来支持。

```
gzip_static on;
gzip_http_version 1.0;
```

ngx\_http\_gzip\_static\_module模块是，对于该模块下的gzip\_vary指令，开启后针对未压缩数据在http响应头添加"vary:Accept-Encoding"头域，而不是所有数据，于是我们不适用gzip\_vary，

```
add_header Vary Accept-Encoding gzip;
```

##Nginx服务器的rewrite功能

###nginx后端服务器组的配置5个指令

>它们是由标准HTTP模块ngx\_http\_upstream\_module进行解析和处理的。

1.upstream指令

设置后端服务器组的主要指令。

```
upstream name {...} 
#name 是指后端服务器组的组名
```

默认情况下，某个服务器组接收到请求以后，按照轮叫调度（RR）策略顺序组内服务器处理请求。如果出现服务器错误，就会把请求进行顺延到下一个服务器进行处理。

2.server指令

该指令用于设置组内的服务器。

```
server address [parameters];
#address 服务器地址 可以是包含端口号的ip地址、域名或者以“unix:”为前缀用于进程间通信的Unix Domain Socket。
#parameters 为当前服务器配置更多属性。
weight=number，为组内服务器设置权重，权重值高的服务器就被优先用于处理请求。组内服务器此时是加权轮询策略，组内默认值是1.
max_fails=number,设置一个请求失败的次数。在一定时间范围内，当对组内某台服务器请求失败次数超过该变量设置的值时，认为该服务器无效。
#404状态不被认为是请求失败
#fail_timeout = number,一是设置max_fails指令尝试某台组内服务器的时间。另外一个就是在检查服务器是否有效时，如果一台服务器被认为down的，该变量设置的时间为认为该服务器无效持续时间。
#down 将某台组内服务器标记为永久无效，通常与ip_hash指令配合使用。
#backup 将某台组内服务器标记为备用服务器，只有当正常的服务器处于无效状态或者繁忙状态时，该服务器才会被启用。
```

```
upstream backend 
{
	server backend1.jv.com weight=5;
	server 127.0.0.1:8080 max_fails=3 fail_timeout=30;
	server unix:/tmp/backend3;
}

```

3.ip_hash指令

该指令用于会话保持功能，将某个客户端的多次请求定向到组内同一台服务器上，保证客户端与服务器之间建立稳定的会话。

```
ip_hash;
```

ip_hash指令不能与server指令中的weight同时使用。ip_hash指令技术主要根据客户端ip地址分配服务器，因此在整个系统中，nginx服务器应该处于最前端的服务器，否则取不到客户端的地址，而且客户端的地址必须是c类地址。

```
upstream backend 
{
	ip_hash;
	server jv.aihuishou.com;
	server jv2.aihuishou.com;
}
#使用ip_hash之后，使用同一个客户端想nginx服务器发送请求报告时，都是jv.aihuishou.com做出响应。
```

4.keepalive指令

该指令用于控制网络连接保持功能。通过该指令，能够保证nginx服务器的工作进程为服务器组内打开一部分网络连接，并将数量控制在一定范围内。

```
keepalive connection;
#connection 为nginx服务器的每一个工作进程允许该服务器组保持的空闲网络连接数的上限值。
```

5.least_conn指令

该指令用于配置nginx服务器使用负载均衡策略为网络连接分配服务器组内的服务器。

```
least_conn;
```
