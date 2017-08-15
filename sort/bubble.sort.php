<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/6
 * Time: 上午1:03
 * 冒泡排序
 * 两两比较待排序数据元素的大小，发现两个数据元素的次序相反时即进行交换，直到没有反序的数据元素为止。
 * O(n2)
 */
$num = $argv[1];

for($i=0;$i<$num;$i++){
	$arr[] = rand(1,$num);
}

function bubbleSort($arr){
    for($i=0,$len=count($arr);$i<$len;$i++){
        //两两比较交换
        for($j=$len-1;$j>$i;$j--){
            if($arr[$j]<$arr[$j-1]){
                $tmp = $arr[$j];
                $arr[$j] = $arr[$j-1];
                $arr[$j-1] = $tmp;
            }
        }
    }
    return $arr;
}

var_dump(bubbleSort($arr));
