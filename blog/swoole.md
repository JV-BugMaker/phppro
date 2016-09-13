#初始swoole

>随着nodejs的井喷式发展，很多phper感到了压力，更有甚者悲观的认为PHP会被nodejs替代。
>作为一个坚定的phper，我始终相信php是世界上最好的语言。况且两者之前不存在你死我活的局面，PHP和nodejs各自都有适合的场景。
>nodejs强在异步，但是PHP在很早之前就有了swoole扩展（现在也有现成框架）。直接秒杀nodejs，哈哈。
>swoole是rango带头开发的 <a>http://wiki.swoole.com</a>有兴趣的同学可以去看看官网

##扩展安装

>首先需要在你的机器上安装swoole扩展。
>下载swoole的安装包
>swoole的编译安装会依赖一些常用的包，这边就不列举了。

1.下载源码<a href='https://github.com/swoole/swoole-src/releases'>swoole扩展下载</a> 

2.cd swoole-src-swoole-1.7.6-stable/

3.phpize

4../configure

5.sudo make

6.sudo make install

7.在php.ini文件中增加extension=swoole.so

8.在终端查看php -m 查看是否存在swoole



##创建server服务器

```
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/13
 * Time: 17:52
 */

class Server
{
    private $serv;

    public function __construct(){
        //创建swoole服务器
        $this->serv = new swoole_server("0.0.0.0",9501);
        $this->serv->set(array(
            'worker_num'=>8,
            'daemonize'=>false
        ));
        //四个状态 开启 链接 接收 结束 进行回调
        $this->serv->on('Start',array($this,'onStart'));
        $this->serv->on('Connect',array($this,'onConnect'));
        $this->serv->on('Receive',array($this,'onReceive'));
        $this->serv->on('Close',array($this,'onClose'));

        //服务器开启
        $this->serv->start();
    }

    public function onStart($serv){
        echo "Start \n";
    }

    public function onConnect($serv,$fd,$from_id){
        $serv->send($fd,"Hello {$fd}");
    }
    public function onReceive(swoole_server $serv,$fd,$from_id,$data){
        echo "Get Message From Client {$fd}:{$data}\n";
        $serv->send($fd,$data);
    }

    public function onClose($serv,$fd,$from_id){
        echo "Client {$fd} close connect\n";
    }
}
//启动服务器
$server = new Server();
```

##创建client


```
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/13
 * Time: 18:05
 * client客户端
 */

class Client
{
    private $client;

    public function __construct(){
        $this->client = new swoole_client(SWOOLE_SOCK_TCP);

    }

    public function connect(){
        if(!$this->client->connect("127.0.0.1",9501,1)){
            echo "Error:{$this->client->errMsg}[{$this->client->errCode}]\n";
        }
        //连接完成之后发送消息 在接收到从服务器发送来的消息
        fwrite(STDOUT,'请输入消息');
        $msg = trim(fgets(STDIN));
        $this->client->send($msg);

        $message = $this->client->recv();
        //接收完成后 connect就关闭了
        echo "GET Message From Server:{$message}";
    }
}

$client = new Client();
$client->connect();
```

>使用命令分别开启server 和client进行简单的体验

