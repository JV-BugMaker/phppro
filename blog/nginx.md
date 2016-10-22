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
