<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/7
 * Time: 上午12:02
 * PHP容易进入陷阱的问题整理
 */

$arr = array(0=>1,'aa'=>2,3,4);

foreach($arr as $key=>$value){
    echo ($key=='aa')?5:$value;
}
//正确答案是 5534
//问题点就是在于 ==  以及PHP存在的隐式转换 'aa'在于整型比较的时候 会发生隐式转换 转换成0

echo "\n";
$i = '11';

printf('%d\n',printf('%d',printf('%d',$i)));
//正确答案是1121  注意点就是 printf()是个函数  会有返回值 返回值就是字符串长度

echo "\n";
$a = 3;
$b = 5;
if($a = 5 || $b = 7) {
    $a++;
    $b++;
}
echo $a . " " . $b;
//正确答案 26
//重点在if中的语句 首先发生的就是 $a = 5  true  $a转换成布尔值的问题 $a -- 5 -- 1  --2  b--5--6


$count = 5;
function get_count() {
    static $count = 0;
    return $count++;
}
++$count;
get_count();
echo get_count();
//函数中static 是个干扰值


//1+0+1  count(null) -- 0   count(false) -- count(0) == 1
$a = count ("567")  + count(null) + count(false);
echo $a;


//nginx是应用层 所以使用的网络协议 底层就是基础的TCP/IP 网络层就是http fastcgi负责调度

//<? echo "asdasda"; 没有输出内容 需要考虑配置文件中的short_open_tag 是否开启



//<!--TINYINT -2^7 - 2^7-1   unsigned 0 ~ 2^8-1-->
//<!--SMALLINT-2^15 - 2^15-1  unsigned 0 ~ 2^16-1-->
//<!--MEDIUMINT-2^23 - 2^23-1  unsigned 0 ~ 2^24-1-->
//<!--INT-2^31 - 2^31-1 unsigned 0 ~ 2^32-1 -->


//func_num_args() func_get_arg() unc_get_args()
//获取函数参数 信息

//call_user_func_array() 更加简单
//mysql_real_escape_string

$a="hello";
$b= &$a;
unset($b);
$b="world";
echo $a;

//empty 对 ""、0、"0"、NULL、FALSE、array()、var $var; 返回都是true
//首先要理解strmp（$str1，$str2）函数的意思，比较两个字符串的大小,比较时计算了两个字符串相差（不同）字符的个数一起作为返回
//在php5，一个对象变量已经不再保存整个对象的值。只是保存一个标识符来访问真正的对象内容。 当对象作为参数传递，作为结果返回，或者赋值给另外一个变量，
//另外一个变量跟原来的不是引用的关系，只是他们都保存着同一个标识符的拷贝，这个标识符指向同一个对象的真正内容。
//PDO_OCI is a driver that implements the  PHP Data Objects (PDO) interface  to enable access from PHP to Oracle databases through the OCI library.

//
////① PDO::ERRMODE_SILENT:不报错误
//② PDO::ERRMODE_WARNING:以警告的方式报错
//③ PDO::ERRMODE_EXCEPTION：以异常的方式报错
//发送DML语句 --- 针对查询语句