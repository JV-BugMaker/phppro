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