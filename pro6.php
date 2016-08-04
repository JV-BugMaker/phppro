<?php
var_dump();
var_export();
//说实话 我一般都是用dump的 export基本没怎么用
//但是多学点总是好的嘛
//两个都是能够将代码值 打印出来
//var_export必须返回合法的php代码， 也就是说，var_export返回的代码，可以直接当作php代码赋值个一个变量。
//而这个变量就会取得和被var_export一样的类型的值

//也就说如果这个变量是一个资源类型 或者对象类型的 export就会抛出null  而dump不会
//所以得出一个结论就是 别用export  用dump接没事了  哈哈哈

//php简单的异步处理耗时脚本的办法

$fp = fsockopen("www.example.com", 80, $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
    $out = "GET /backend.php  / HTTP/1.1\r\n";
    $out .= "Host: www.example.com\r\n";
    $out .= "Connection: Close\r\n\r\n";

    fwrite($fp, $out);
    /*忽略执行结果
    while (!feof($fp)) {
        echo fgets($fp, 128);
    }*/
    fclose($fp);
}

//触发远程脚本比如说 mysql的io操作 但是前提是我们不需要关心这个结果是什么
//
function triggerRequest($url, $post_data = array(), $cookie = array())…{
      //拼接http header头
     $method = "GET";  //可以通过POST或者GET传递一些参数给要触发的脚本
     $url_array = parse_url($url); //获取URL信息，以便平凑HTTP HEADER
     $port = isset($url_array['port'])? $url_array['port'] : 80;

     $fp = fsockopen($url_array['host'], $port, $errno, $errstr, 30);
     if (!$fp) …{
             return FALSE;
     }
     $getPath = $url_array['path'] ."?". $url_array['query'];
     if(!empty($post_data))…{
             $method = "POST";
     }
     $header = $method . " " . $getPath;
     $header .= " HTTP/1.1\r\n";
     $header .= "Host: ". $url_array['host'] . "\r\n "; //HTTP 1.1 Host域不能省略
     /**//*以下头信息域可以省略
     $header .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13 \r\n";
     $header .= "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,q=0.5 \r\n";
     $header .= "Accept-Language: en-us,en;q=0.5 ";
     $header .= "Accept-Encoding: gzip,deflate\r\n";
      */
      $header .= "Connection:Close\r\n";
        if(!empty($cookie))…{
                $_cookie = strval(NULL);
                foreach($cookie as $k => $v)…{
                        $_cookie .= $k."=".$v."; ";
                }
                $cookie_str =  "Cookie: " . base64_encode($_cookie) ." \r\n";//传递Cookie
                $header .= $cookie_str;
        }
        if(!empty($post_data))…{
                $_post = strval(NULL);
                foreach($post_data as $k => $v)…{
                        $_post .= $k."=".$v."&";
                }
                $post_str  = "Content-Type: application/x-www-form-urlencoded\r\n";//POST数据
                $post_str .= "Content-Length: ". strlen($_post) ." \r\n";//POST数据的长度
                $post_str .= $_post."\r\n\r\n "; //传递POST数据  http协议中的空一行
                $header .= $post_str;
        }
        fwrite($fp, $header);
        //echo fread($fp, 1024); //我们不关心服务器返回
        fclose($fp);
        return true;
}
//php 使用sock过去通信的核心点 其实就是伪造好http协议进行完美的触发 之后就关闭通道
//也就是让targe 进行服务器端自己执行 以及相应的一些反应
