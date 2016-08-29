<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/8/30
 * Time: 上午12:14
 * thema:PHP哈希冲突实例 hash冲突能够实现对服务器的拒绝访问
 */
$size = pow(2,16);
$startTime = microtime();
$arr = array();
for($key = 0,$maxKey = ($size-1) * $size;$key<=$maxKey;$key += $size){
    $arr[$key] = 0;
}
$endTime = microtime();
$str = "插入{$size}个元素,耗时".($endTime-$startTime)."秒";
for($key = 0,$maxKey = $size - 1;$key<=$maxKey;$key++){
    $arr[$key] = 0;
}
$endTime2 = microtime();
$str2 = "插入{$size}个元素,耗时".($endTime2-$endTime)."秒";
var_dump($str,$str2);
//在PHP中,如果键值是数字, 那么Hash的时候就是数字本身, 一般的时候都是, index & tableMask.
// 而tableMask是用来保证数字索引不会超出数组可容纳的元素个数值, 也就是数组个数-1.

//PHP的Hashtable的大小都是2的指数, 比如如果你存入10个元素的数组, 那么数组实际大小是16, 如果存入20个, 则实际大小为32, 而63个话, 实际大小为64.
// 当你的存入的元素个数大于了数组目前的最多元素个数的时候, PHP会对这个数组进行扩容, 并且从新Hash.

//现在, 我们假设要存入64个元素(中间可能会经过扩容, 但是我们只需要知道, 最后的数组大小是64, 并且对应的tableMask为63:0111111),
// 那么如果第一次我们存入的元素的键值为0, 则hash后的值为0, 第二次我们存入64,
// hash(1000000 & 0111111)的值也为0, 第三次我们用128, 第四次用192…
// 就可以使得底层的PHP数组把所有的元素都Hash到0号bucket上, 从而使得Hash表退化成链表了.  也就是说这样是进过特殊的构造使得hash都落到了0 号bucket上面
//进而变成了 链表了 遍历链表速度慢
